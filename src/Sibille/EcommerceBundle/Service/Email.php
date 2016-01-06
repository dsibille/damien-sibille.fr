<?php
namespace Sibille\EcommerceBundle\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;
/**
 * Description of Email
 *
 * @author Damien
 */
class Email {
    
    protected $container;
    protected $smtp;
    protected $username;
    protected $password;
    protected $email_from;
    protected $email_from_name;
    protected $email_to;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->smtp = $this->container->getParameter('mailer_host');
        $this->username = $this->container->getParameter('mailer_user');
        $this->password = $this->container->getParameter('mailer_password');
        $this->email_from = $this->container->getParameter('email_from');
        $this->email_from_name = 'damien-sibille.fr';
        $this->email_to = $this->container->getParameter('email_to');
    }
               
    public function envoyerMessage($form) {

        // récupération des données du formulaire de contact
        $nom = $form->get('nom')->getData();
        $prenom = $form->get('prenom')->getData();
        $email = $form->get('email')->getData();
        $telephone = $form->get('telephone')->getData();
        $commentaire = $form->get('commentaire')->getData();
        $message = array('nom' => $nom, 'prenom' => $prenom, 'email' => $email, 'telephone' => $telephone, 'commentaire' => $commentaire);      
        
        $transport = \Swift_SmtpTransport::newInstance($this->smtp, 465, 'ssl')->setUsername($this->username)->setPassword($this->password);
        $message = \Swift_Message::newInstance()        
            ->setSubject('Nouveau message de contact')
            ->setFrom(array($this->email_from => $this->email_from_name))
            ->setTo($this->email_to)
            ->setBody($this->container->get('templating')->render('SibilleEcommerceBundle:email:contact.html.twig', array('message' => $message)), 'text/html');                    
        $mailer = \Swift_Mailer::newInstance($transport);
        $mailer->send($message);

    }
    
}
