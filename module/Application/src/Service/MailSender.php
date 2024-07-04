<?php

namespace Application\Service;

use Laminas\View\Model\ViewModel;
use Laminas\Mail\Transport\Smtp;
use Laminas\Mail\Transport\SmtpOptions;
use Laminas\Mail\Message;
use Laminas\Mime\Message as MimeMessage;
use Laminas\Mime\Part as MimePart;
use Laminas\Mime;

class MailSender {
    
    private $serviceManager;
    
    public function __construct($serviceManager) 
    {
        $this->serviceManager = $serviceManager;
    }
    
    public function sendMail($to,$subject,$dataMail,$plantilla,$pathToFile=null,$fileName=null,$cc=null,$feria=null){
        $config = $this->serviceManager->get('Config');
        $list_attachment_type=[
            "xml"=>"text/xml",
            "pdf"=>"application/pdf",
            "excel"=>"application/vnd.ms-excel",
            "png"=>"image/png",
            "jpg"=>"image/jpeg",
            "jpeg"=>"image/jpeg",
            "zip"=>"application/zip",
            "gif"=>"image/gif",
            "svg"=>"image/svg+xml",
            "docx"=>"application/msword",
            "docx"=>"application/vnd.openxmlformats-officedocument.wordprocessingml.document"
        ];
        $message = new Message();
        $message->setEncoding('UTF-8');
        $body = new MimeMessage();
        $message->addFrom($config['mail']['from'],$feria);
        $message->setSubject($subject);
        if(!filter_var($to, FILTER_VALIDATE_EMAIL))return;
        $message->addTo(strtolower($to));
        if(isset($config['servidor']) && $config['servidor'] === 'desarrollo')$cc = null;
        if($cc != null && !empty($cc)){
            foreach($cc as $mail_cc){
                $message->addCc(strtolower($mail_cc));
            }
        }
        if($pathToFile != null){
            $partes_ruta = pathinfo($pathToFile);
            $file_extension = trim(strtolower($partes_ruta['extension']));
            $attachment = new MimePart(fopen($pathToFile, 'r'));
            $attachment->type = $list_attachment_type[strtolower($file_extension)];
            $attachment->filename = $fileName;
            $attachment->encoding = Mime\Mime::ENCODING_BASE64;
            $attachment->disposition = Mime\Mime::DISPOSITION_ATTACHMENT;
            $body->addPart($attachment);
        }
        /***** BODY TEMPLATE MAIL [INICIO] *****/
        $view = new ViewModel($dataMail);
        $view->setTemplate('mail/template-'.$plantilla);
        $view->setTerminal(true);
        $emailBody = $this->serviceManager->get('ViewRenderer')->render($view);
        /***** BODY TEMPLATE MAIL [FIN] *****/
        $html = new MimePart($emailBody);
        $html->type = "text/html;charset = UTF-8";
        $body->addPart($html);
        $transport = new Smtp();
        $options = new SmtpOptions($config['mail']['smtp_options']);
        $transport->setOptions($options);
        $message->setBody($body);
        $transport->send($message);
    }
}