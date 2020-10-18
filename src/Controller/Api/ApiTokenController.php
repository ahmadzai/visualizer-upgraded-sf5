<?php
/**
 * Created by PhpStorm.
 * User: Wazir
 * Date: 1/29/2019
 * Time: 8:44 AM
 */

namespace App\Controller\Api;


use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTEncodeFailureException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\HttpFoundation\JsonResponse;


class ApiTokenController extends AbstractController
{
    /**
     * @Route("/auth/token", methods={"GET"})
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param JWTEncoderInterface $encoder
     * @return JsonResponse
     */
    public function newTokenAction(Request $request, UserPasswordEncoderInterface $passwordEncoder, JWTEncoderInterface $encoder)
    {

        $user = $this->getDoctrine()
            ->getRepository(User::class)
            ->findOneBy(['username' => $request->getUser()]);

        if (!$user) {
            return new JsonResponse(['error' => 'Username or password is incorrect!'], 403);
        }


        $isValid = $passwordEncoder
            ->isPasswordValid($user, $request->getPassword());

        if (!$isValid) {
            //throw new CustomUserMessageAuthenticationException("Your are not allowed to access Token");
            return new JsonResponse(['error' => 'Username or password is incorrect!'], 401);
        }

        // if user were not allowed for api access
        if(!$user->getHasApiAccess()) {
            //throw new CustomUserMessageAuthenticationException("Your are not allowed to access Token");
            return new JsonResponse(['error' => 'You are not allowed to access token'], 401);
        }

        try {
            $token = $encoder
                ->encode(['username' => $user->getUsername()]);
        } catch (JWTEncodeFailureException $e) {
            return new JsonResponse(['error' => 'Token can not be granted at this time, try again later!'], 401);
        }


        return new JsonResponse(['token' => $token], 200);
    }

}
