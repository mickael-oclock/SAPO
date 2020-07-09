<?php

namespace App\Controller;

use App\Form\UserTokenType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    /**
     * @Route("/ajouter-token", name="app_set_token")
     */
    public function setToken(Request $request)
    {
        $user = $this->getUser();
        $formOptions = ["method" => Request::METHOD_POST,"action" => $this->generateUrl("app_set_token")];
        $form = $this->createForm(UserTokenType::class,$user,$formOptions);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            $this->addFlash('success',"Votre token est mis à jour");
            return $this->redirectToRoute('promo');
        }else{
            // todo handle stuff here if needed
        }

        return $this->render('security/setToken.html.twig',[
            'form' => $form->createView()
        ]);
    }
}
