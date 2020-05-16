<?php
/**
 * Created by PhpStorm.
 * User: Wazir
 * Date: 1/29/2019
 * Time: 8:44 AM
 */

namespace App\Controller\Api;


use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTEncodeFailureException;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;


class ApiTokenController extends AbstractController
{
    /**
     * @Route("/api/token", methods={"GET"})
     * @param Request $request
     * @return JsonResponse
     */
    public function newTokenAction(Request $request)
    {


        //return new JsonResponse($request->getPassword(), 200);
        $user = $this->getDoctrine()
            ->getRepository('App:User')
            ->findOneBy(['username' => $request->getUser()]);

        if (!$user) {
            throw $this->createNotFoundException();
        }

        $isValid = $this->get('security.password_encoder')
            ->isPasswordValid($user, $request->getPassword());
        if (!$isValid) {
            //throw new CustomUserMessageAuthenticationException("Your are not allowed to access Token");
            throw new BadCredentialsException();
        }

        // if user were not allowed for api access
        if(!$user->isAllowApiAccess()) {
            throw new CustomUserMessageAuthenticationException("Your are not allowed to access Token");
        }

        try {
            $token = $this->get('lexik_jwt_authentication.encoder')
                ->encode(['username' => $user->getUsername()]);
        } catch (JWTEncodeFailureException $e) {
            throw new CustomUserMessageAuthenticationException("Token can't be granted");
        }


        return new JsonResponse(['token' => $token]);
    }

}
