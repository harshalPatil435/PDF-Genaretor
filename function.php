<?php
$path = get_template_directory() . '/vendor/autoload.php';		
        require_once($path);
        
        try {
            extract($_POST);
			$current_user = wp_get_current_user();
            $pdf = get_post_meta($post_id, 'attachment', true);
            ob_start();
            include(get_template_directory() . '/quotation-pdf.php');
            $pdf_body = ob_get_clean();
            	
            // $pdf_body = file_get_contents(get_template_directory() . '/quotation-pdf.php');
            $html = $pdf_body;
            
            header('Cache-Control: no-cache, no-store, must-revalidate'); // HTTP 1.1
            header('Pragma: no-cache'); // HTTP 1.0
            header('Expires: 0'); // Proxies
           
            $mpdf = new \Mpdf\Mpdf();

            $mpdf->WriteHTML("$html");
            $filename = 'quotation_'. $post_id . '.pdf'; 
            $filepath = get_template_directory() . '/pdf/' . $filename;
           // $filepath = get_template_directory().'/pdf/quotation.pdf';
     
            $mpdf->Output($filepath,'F');
        
            $quote_file = get_template_directory_uri() . '/pdf/' . $filename;

            // Get the recipient email address
            $to = $current_user->user_email;
            $subject = 'PDF Attachment';
            $body = 'Here is the Quotation PDF attachment.';
            $body .= "Signature form:" .$signature_link.'/?pdf='.$quote_file.'&email='.$to;
            $headers = array(
                'Content-Type: text/html; charset=UTF-8',
            );
            $attachments = array( $filepath );
            update_post_meta($post_id, 'attachment', $quote_file);
            wp_mail( $cd_mail, $subject, $body, $headers, $attachments );



        } catch (\Mpdf\MpdfException $e) { 
        
            echo $e->getMessage();
        }