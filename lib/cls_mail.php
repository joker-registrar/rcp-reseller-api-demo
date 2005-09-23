<?php
/*******************************************************************************
    Name:           Email
    Description:    This class is used for sending emails.
            These emails can be
            Plain Text, HTML, or Both. Other uses include file
            Attachments and email Templates(from a file).
    Testing:
        test_email.php3:

        $mail->setTo("myEmail@yo.com");
        $mail->send();


    Changelog:
    Date        Name            Description
    ----------- -----------     ------------------------------------------------
    10/21/1999  R.Chambers      created
*******************************************************************************/
/*******************************************************************************
    Issues:
        no error reporting
        can only send HTML with TEXT
        can only send attachements with HTML and TEXT
*******************************************************************************/
/*******************************************************************************
    Function Listing:
        setTo($inAddress)
        setCC($inAddress)
        setBCC($inAddress)
        setFrom($inAddress)
        setSubject($inSubject)
        setText($inText)
        setHTML($inHTML)
        setAttachments($inAttachments)
        checkEmail($inAddress)
        loadTemplate($inFileLocation,$inHash,$inFormat)
        getRandomBoundary($offset)
        getContentType()
        formatTextHeader()
        formatHTMLHeader()
        formatAttachmentHeader($inFileLocation)
        send()
*******************************************************************************/

class Email
{
    //---Global Variables
    var $mailTo     = "";                       // array of To addresses
    var $mailReplyTo        = "";                       // array of To addresses
    var $mailCC     = "";                       // copied recipients
    var $mailBCC        = "";                       // hidden recipients
    var $mailFrom       = "";                       // from address
    var $mailSubject    = "";                       // email subject
    var $mailText       = "";                       // plain text message
    var $mailHTML       = "";                       // html message
    var $mailAttachments    = "";                       // array of attachments
    var $mailAddCmdLnParam  = "";                       // additional command line parameters

/*******************************************************************************
    Function:       setTo($inAddress)
    Description:    sets the email To address
    Arguments:      $inAddress as string
                    separate multiple values with comma
    Returns:        true if set
*******************************************************************************/
    function setTo($inAddress){
        //--split addresses at commas
        $addressArray = explode(",",$inAddress);
        //--loop through each address and exit on error
        for($i=0;$i<count($addressArray);$i++){
            if($this->checkEmail($addressArray[$i])==false) return false;
        }
        //--all values are OK so implode array into string
        $this->mailTo = implode($addressArray,",");
        return true;
    }
/*******************************************************************************
    Function:       setReplyTo($inAddress)
    Description:    sets the email reply-to address
    Arguments:      $inAddress as string
                    separate multiple values with comma
    Returns:        true if set
*******************************************************************************/
    function setReplyTo($inAddress){
        //--split addresses at commas
        $addressArray = explode(",",$inAddress);
        //--loop through each address and exit on error
        for($i=0;$i<count($addressArray);$i++){
            if($this->checkEmail($addressArray[$i])==false) return false;
        }
        //--all values are OK so implode array into string
        $this->mailReplyTo = implode($addressArray,",");
        return true;
    }
/*******************************************************************************
    Function:       setCC($inAddress)
    Description:    sets the email cc address
    Arguments:      $inAddress as string
                    separate multiple values with comma
    Returns:        true if set
*******************************************************************************/
    function setCC($inAddress){
        //--split addresses at commas
        $addressArray = explode(",",$inAddress);
        //--loop through each address and exit on error
        for($i=0;$i<count($addressArray);$i++){
            if($this->checkEmail($addressArray[$i])==false) return false;
        }
        //--all values are OK so implode array into string
        $this->mailCC = implode($addressArray,",");
        return true;
    }
/*******************************************************************************
    Function:       setBCC($inAddress)
    Description:    sets the email bcc address
    Arguments:      $inAddress as string
                    separate multiple values with comma
    Returns:        true if set
*******************************************************************************/
    function setBCC($inAddress){
        //--split addresses at commas
        $addressArray = explode(",",$inAddress);
        //--loop through each address and exit on error
        for($i=0;$i<count($addressArray);$i++){
            if($this->checkEmail($addressArray[$i])==false) return false;
        }
        //--all values are OK so implode array into string
        $this->mailBCC = implode($addressArray,",");
        return true;
    }
/*******************************************************************************
    Function:       setFrom($inAddress)
    Description:    sets the email FROM address
    Arguments:      $inAddress as string (takes single email address)
    Returns:        true if set
*******************************************************************************/
    function setFrom($inAddress){
        if($this->checkEmail($inAddress)){
            $this->mailFrom = $inAddress;
            return true;
        }
        return false;
    }
/*******************************************************************************
    Function:       setSubject($inSubject)
    Description:    sets the email subject
    Arguments:      $inSubject as string
    Returns:        true if set
*******************************************************************************/
    function setSubject($inSubject){
        if(strlen(trim($inSubject)) > 0){
            $this->mailSubject = ereg_replace("\n","",$inSubject);
            return true;
        }
        return false;
    }
/*******************************************************************************
    Function:       setText($inText)
    Description:    sets the email text
    Arguments:      $inText as string
    Returns:        true if set
*******************************************************************************/
    function setText($inText){
        if(strlen(trim($inText)) > 0){
            $this->mailText = $inText;
            return true;
        }
        return false;
    }
/*******************************************************************************
    Function:       setHTML($inHTML)
    Description:    sets the email HMTL
    Arguments:      $inHTML as string
    Returns:        true if set
*******************************************************************************/
    function setHTML($inHTML){
        if(strlen(trim($inHTML)) > 0){
            $this->mailHTML = $inHTML;
            return true;
        }
        return false;
    }
/*******************************************************************************
    Function:       setAttachments($inAttachments)
    Description:    stores the Attachment string
    Arguments:      $inAttachments as string with directory included
                    separate multiple values with comma
    Returns:        true if stored
*******************************************************************************/
    function setAttachments($inAttachments){
        if(strlen(trim($inAttachments)) > 0){
            $this->mailAttachments = $inAttachments;
            return true;
        }
        return false;
    }
/*******************************************************************************
    Function:       setAddCmdLnParams($inParameter)
    Description:    sets additional command line parameter, especially useful for handling bounces
    Arguments:      $inParameter as string
    Returns:        true if set
*******************************************************************************/
    function setAddCmdLnParams($inParameter){
        //--sets the var
        $this->mailAddCmdLnParam = $inParameter;
        return true;
    }
/*******************************************************************************
    Function:       checkEmail($inAddress)
    Description:    checks for valid email
    Arguments:      $inAddress as string
    Returns:        true if valid
*******************************************************************************/
    function checkEmail($inAddress){
        $this->tool = new Tools;
        $res = $this->tool->is_valid("email",$inAddress,true);
        if (!$res && $inAddress == "") {
            $res = true;
        }
        return ($res);
    }
/*******************************************************************************
    Function:       loadTemplate($inFileLocation,$inHash,$inFormat)
    Description:    reads in a template file and replaces hash values
    Arguments:      $inFileLocation as string with relative directory
                    $inHash as Hash with populated values
                    $inFormat as string either "text" or "html"
    Returns:        true if loaded
*******************************************************************************/
    function loadTemplate($inFileLocation,$inHash,$inFormat){
        /*
        template files have lines such as:
            Dear ~!UserName~,
            Your address is ~!UserAddress~
        */
        //--specify template delimeters
        $templateDelim = "~";
        $templateNameStart = "!";
        //--set out string
        $templateLineOut = "";
        //--open template file
        if($templateFile = fopen($inFileLocation,"r")){
            //--loop through file, line by line
            while(!feof($templateFile)){
                //--get 1000 chars or (line break internal to fgets)
                $templateLine = fgets($templateFile,1000);
                //--split line into array of hashNames and regular sentences
                $templateLineArray = explode($templateDelim,$templateLine);
                //--loop through array
                for( $i=0; $i<count($templateLineArray);$i++){
                    //--look for $templateNameStart at position 0
                    if(strcspn($templateLineArray[$i],$templateNameStart)==0){
                        //--get hashName after $templateNameStart
                        $hashName = substr($templateLineArray[$i],1);
                        //--replace hashName with acual value in $inHash
                        //--(string) casts all values as "strings"
                        $templateLineArray[$i] = ereg_replace($hashName,(string)$inHash[$hashName],$hashName);
                    }
                }
                //--output array as string and add to out string
                $templateLineOut .= implode($templateLineArray,"");
            }
            //--close file
            fclose($templateFile);
            //--set Mail body to proper format
            if( strtoupper($inFormat)=="TEXT" ) return($this->setText($templateLineOut));
            else if( strtoupper($inFormat)=="HTML" ) return($this->setHTML($templateLineOut));
        }
        return false;
    }
/*******************************************************************************
    Function:       getRandomBoundary($offset)
    Description:    returns a random boundary
    Arguments:      $offset as integer - used for multiple calls
    Returns:        string
*******************************************************************************/
    function getRandomBoundary($offset = 0){
        //--seed random number generator
        srand(time()+$offset);
        //--return md5 32 bits plus 4 dashes to make 38 chars
        return ("----".(md5(rand())));
    }
/*******************************************************************************
    Function:       getContentType($inFileName)
    Description:    returns content type for the file type
    Arguments:      $inFileName as file name string (can include path)
    Returns:        string
*******************************************************************************/
    function getContentType($inFileName){
        //--strip path
        $inFileName = basename($inFileName);
        //--check for no extension
        if(strrchr($inFileName,".") == false){
            return "application/octet-stream";
        }
        //--get extension and check cases
        $extension = strrchr($inFileName,".");
        switch($extension){
        case ".gif":    return "image/gif";
        case ".gz": return "application/x-gzip";
                case ".htm":    return "text/html";
                case ".html":   return "text/html";
                case ".jpg":    return "image/jpeg";
                case ".tar":    return "application/x-tar";
        case ".txt":    return "text/plain";
                case ".zip":    return "application/zip";
                case ".pdf":    return "application/pdf";
            default:        return "application/octet-stream";
        }
        return "application/octet-stream";
    }
/*******************************************************************************
    Function:       formatTextHeader
    Description:    returns a formated header for text
    Arguments:      none
    Returns:        string
*******************************************************************************/
    function formatTextHeader(){
        $outTextHeader = "";
        $outTextHeader .= "Content-Type: text/plain; charset=us-ascii\n";
        $outTextHeader .= "Content-Transfer-Encoding: 7bit\n\n";
        $outTextHeader .= $this->mailText."\n";
        return $outTextHeader;
    }
/*******************************************************************************
    Function:       formatHTMLHeader
    Description:    returns a formated header for HTML
    Arguments:      none
    Returns:        string
*******************************************************************************/
    function formatHTMLHeader(){
        $outHTMLHeader = "";
        $outHTMLHeader .= "Content-Type: text/html; charset=us-ascii\n";
        $outHTMLHeader .= "Content-Transfer-Encoding: 7bit\n\n";
        $outHTMLHeader .= $this->mailHTML."\n";
        return $outHTMLHeader;
    }
/*******************************************************************************
    Function:       formatAttachmentHeader($inFileLocation)
    Description:    returns a formated header for an attachment
    Arguments:      $inFileLocation as string with relative directory
    Returns:        string
*******************************************************************************/
    function formatAttachmentHeader($inFileLocation){
        $outAttachmentHeader = "";
        //--get content type based on file extension
        $contentType = $this->getContentType($inFileLocation);
        //--if content type is TEXT the standard 7bit encoding
        if(ereg("text",$contentType)){
            //--format header
            $outAttachmentHeader .= "Content-Type: ".$contentType.";\n";
            $outAttachmentHeader .= ' name="'.basename($inFileLocation).'"'."\n";
            $outAttachmentHeader .= "Content-Transfer-Encoding: 7bit\n";
            $outAttachmentHeader .= "Content-Disposition: attachment;\n";   //--other: inline
            $outAttachmentHeader .= ' filename="'.basename($inFileLocation).'"'."\n\n";
            $textFile = fopen($inFileLocation,"r");
            //--loop through file, line by line
            while(!feof($textFile)){
                //--get 1000 chars or (line break internal to fgets)
                $outAttachmentHeader .= fgets($textFile,1000);
            }
            //--close file
            fclose($textFile);
            $outAttachmentHeader .= "\n";
        }
        //--NON-TEXT use base64 encoding
        else{
            //--format header
            $outAttachmentHeader .= "Content-Type: ".$contentType.";\n";
            $outAttachmentHeader .= ' name="'.basename($inFileLocation).'"'."\n";
            $outAttachmentHeader .= "Content-Transfer-Encoding: base64\n";
            $outAttachmentHeader .= "Content-Disposition: attachment;\n";   //--other: inline
            $outAttachmentHeader .= ' filename="'.basename($inFileLocation).'"'."\n\n";
            $fd=fopen($inFileLocation,"r");
            $outAttachmentHeader .= chunk_split(base64_encode(fread($fd,filesize($inFileLocation))),72);
            fclose($fd);
            //--call uuencode - output is returned to the return array
            //exec("uuencode -m $inFileLocation nothing_out",$returnArray);
            //--add each line returned
            //for ($i = 1; $i<(count($returnArray)); $i++){
            //  $outAttachmentHeader .= $returnArray[$i]."\n";
            //}
        }
        return $outAttachmentHeader;
    }
/*******************************************************************************
    Function:       send()
    Description:    sends the email
    Arguments:      none
    Returns:        true if sent
*******************************************************************************/
    function send(){
        //--set  mail header to blank
        $mailHeader = "";
        //--add ReplyTo
        if($this->mailReplyTo != "") $mailHeader .= "Reply-To: ".$this->mailReplyTo."\n";
        //--add CC
        if($this->mailCC != "") $mailHeader .= "CC: ".$this->mailCC."\n";
        //--add BCC
        if($this->mailBCC != "") $mailHeader .= "BCC: ".$this->mailBCC."\n";
        //--add From
        if($this->mailFrom != "") $mailHeader .= "FROM: ".$this->mailFrom."\n";

        //---------------------------MESSAGE TYPE-------------------------------
        //--TEXT ONLY
        $HAS_TEXTONLY       = (!empty($this->mailText) &&  empty($this->mailHTML) &&  empty($this->mailAttachments));
        $HAS_TEXTHTML       = (!empty($this->mailText) && !empty($this->mailHTML) &&  empty($this->mailAttachments));
        $HAS_TEXTHTMLFILE   = (!empty($this->mailText) && !empty($this->mailHTML) && !empty($this->mailAttachments));
        $HAS_TEXTFILE       = (!empty($this->mailText) &&  empty($this->mailHTML) && !empty($this->mailAttachments));

        if($HAS_TEXTONLY){
            return mail($this->mailTo,$this->mailSubject,$this->mailText,$mailHeader,$this->mailAddCmdLnParam);
        }
        //--HTML AND TEXT
        else if($HAS_TEXTHTML){
            //--get random boundary for content types
            $bodyBoundary = $this->getRandomBoundary();
            //--format headers
            $textHeader = $this->formatTextHeader();
            $htmlHeader = $this->formatHTMLHeader();
            //--set MIME-Version
            $mailHeader .= "MIME-Version: 1.0\n";
            //--set up main content header with boundary
            $mailHeader .= "Content-Type: multipart/alternative;\n";
            $mailHeader .= ' boundary="'.$bodyBoundary.'"';
            $mailHeader .= "\n\n\n";
            //--add body and boundaries
            $mailHeader .= "--".$bodyBoundary."\n";
            $mailHeader .= $textHeader;
            $mailHeader .= "--".$bodyBoundary."\n";
            //--add html and ending boundary
            $mailHeader .= $htmlHeader;
            $mailHeader .= "\n--".$bodyBoundary."--";
            //--send message
            return mail($this->mailTo,$this->mailSubject,"",$mailHeader,$this->mailAddCmdLnParam);
        }
        //--HTML AND TEXT AND ATTACHMENTS
        else if($HAS_TEXTHTMLFILE || $HAS_TEXTFILE){
            //--get random boundary for attachments
            $attachmentBoundary = $this->getRandomBoundary();
            //--set MIME-Version
            $mailHeader .= "MIME-Version: 1.0\n";
            //--set main header for all parts and boundary
            $mailHeader .= "Content-Type: multipart/mixed;\n";
            $mailHeader .= ' boundary="'.$attachmentBoundary.'"'."\n\n";
            $mailHeader .= "This is a multi-part message in MIME format.\n";
            $mailHeader .= "--".$attachmentBoundary."\n";

            //--TEXT AND HTML--
            //--get random boundary for content types
            $bodyBoundary = $this->getRandomBoundary(1);
            //--format headers
            $textHeader = $this->formatTextHeader();
            $htmlHeader = $this->formatHTMLHeader();
            //--set up main content header with boundary
            $mailHeader .= "Content-Type: multipart/alternative;\n";
            $mailHeader .= ' boundary="'.$bodyBoundary.'"';
            $mailHeader .= "\n\n\n";
            //--add body and boundaries
            $mailHeader .= "--".$bodyBoundary."\n";
            $mailHeader .= $textHeader;
            $mailHeader .= "--".$bodyBoundary;
            if ($HAS_TEXTFILE) {
                //--add html and ending boundary
                $mailHeader .= "\n".$htmlHeader;
                $mailHeader .= "\n--".$bodyBoundary;
            }
            $mailHeader .= "--";
            //--send message
            //--END TEXT AND HTML

            //--get array of attachment filenames
            $attachmentArray = explode(",",$this->mailAttachments);
            //--loop through each attachment
            for($i=0;$i<count($attachmentArray);$i++){
                //--attachment separator
                $mailHeader .= "\n--".$attachmentBoundary."\n";
                //--get attachment info
                $mailHeader .= $this->formatAttachmentHeader($attachmentArray[$i]);
            }
            $mailHeader .= "--".$attachmentBoundary."--";
            return mail($this->mailTo,$this->mailSubject,"",$mailHeader,$this->mailAddCmdLnParam);
        }

        return false;
    }
}
?>