<?php


namespace App\Security;


use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException;
use Lexik\Bundle\JWTAuthenticationBundle\TokenExtractor\AuthorizationHeaderTokenExtractor;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

class JwtAuth extends AbstractGuardAuthenticator
{
    /**
     * @var JWTEncoderInterface
     */
    private $jwtEncoder;
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(JWTEncoderInterface $jwtEncoder, EntityManagerInterface $em)
    {
        $this->jwtEncoder = $jwtEncoder;
        $this->em = $em;
    }

    public function start(Request $request, AuthenticationException $authException = null)
    {
        return new JsonResponse([
            'error' => 'authentication required'
        ], 401);
    }

    public function supports(Request $request)
    {
        //Todo: replacing below code with symfony headers' functions, however, in my case it didn't work
        $headers = apache_request_headers();
        return array_key_exists('Authorization', $headers)
            && preg_match('/Bearer\s((.*)\.(.*)\.(.*))/', $headers['Authorization']) === 1;
    }

    public function getCredentials(Request $request)
    {
//        $extractor = new AuthorizationHeaderTokenExtractor(
//            'Bearer',
//            'Authorization'
//        );
//
//        $token = $extractor->extract($request);
        $headers = apache_request_headers();

        if(!array_key_exists('Authorization', $headers))
            return false;
        else {
            return substr($headers['Authorization'], 7);
        }
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        try {
            $data = $this->jwtEncoder->decode($credentials);
        } catch (JWTDecodeFailureException $e) {

            throw new CustomUserMessageAuthenticationException('Invalid Token');
        }

        if($data == false)
            throw new CustomUserMessageAuthenticationException("Invalid Token");

        $username = $data['username'];
        return $this->em
            ->getRepository('App:User')
            ->findOneBy(['username' => $username]);
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        return true;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        return new JsonResponse(
            "Sorry! Your token is not valid",
            401
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $providerKey)
    {
        return null;
    }

    public function supportsRememberMe()
    {
        return false;
    }
}