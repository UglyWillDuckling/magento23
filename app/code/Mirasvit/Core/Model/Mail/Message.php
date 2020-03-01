<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-core
 * @version   1.2.83
 * @copyright Copyright (C) 2019 Mirasvit (https://mirasvit.com/)
 */


namespace Mirasvit\Core\Model\Mail;

use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\Mail\MailMessageInterface;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;
use Zend\Mime\Mime as MimeMime;

class Message extends \Magento\Framework\Mail\Message
{
    /**
     * @var \Zend\Mail\Message
     */
    private $zendMessage;

    /**
     * @var MimePart[]
     */
    private $attachements = [];

    protected $messageType = self::TYPE_TEXT;

    private $useParent = false;

    public function __construct(ProductMetadataInterface $productMetadata, $charset = 'utf-8')
    {
        parent::__construct($charset);

        if (class_exists('Zend\Mail\Message', false)) { //compatibility with m2.1.x
            $this->zendMessage = new \Zend\Mail\Message();
            $this->zendMessage->setEncoding($charset);
        } else {
            $this->zendMessage = $this;
        }

        if (version_compare($productMetadata->getVersion(), "2.3.0", "<")) {
            $this->useParent = true;
        }
    }

    public function createAttachment($body,
                                     $mimeType    = \Zend_Mime::TYPE_OCTETSTREAM,
                                     $disposition = \Zend_Mime::DISPOSITION_ATTACHMENT,
                                     $encoding    = \Zend_Mime::ENCODING_BASE64,
                                     $filename    = null)
    {
        if ($this->useParent) {
            return parent::createAttachment($body, $mimeType, $disposition, $encoding, $filename);
        }

        $attach = new MimePart($body);
        $attach->setType($mimeType);
        $attach->setDisposition($disposition);
        $attach->setEncoding($encoding);
        $attach->setFileName($filename);

        $this->attachements[] = $attach;
    }

    public function setBody($body)
    {
        if ($this->useParent) {
            return parent::setBody($body);
        }

        if (is_string($body) && $this->messageType === MailMessageInterface::TYPE_HTML) {
            $body = self::createHtmlMimeFromString($body);
        }

        if ($body instanceof \Zend\Mime\Message) {
            foreach ($this->attachements as $attachement) {
                $body->addPart($attachement);
            }
        }
        $this->zendMessage->setBody($body);

        return $this;
    }

    private function createHtmlMimeFromString($htmlBody)
    {
        $htmlPart = new MimePart($htmlBody);
        $htmlPart->setCharset($this->zendMessage->getEncoding());
        $htmlPart->setType(MimeMime::TYPE_HTML);
        $mimeMessage = new MimeMessage();
        $mimeMessage->addPart($htmlPart);
        return $mimeMessage;
    }

    /**
     * {@inheritdoc}
     */
    public function setMessageType($type)
    {
        $this->messageType = $type;
        return parent::setMessageType($type);
    }

    /**
     * {@inheritdoc}
     */
    public function setSubject($subject)
    {
        if ($this->useParent) {
            return parent::setSubject($subject);
        }
        $this->zendMessage->setSubject($subject);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubject()
    {
        if ($this->useParent) {
            return parent::getSubject();
        }

        return $this->zendMessage->getSubject();
    }

    /**
     * {@inheritdoc}
     */
    public function getBody()
    {
        if ($this->useParent) {
            return parent::getBody();
        }

        return $this->zendMessage->getBody();
    }

    /**
     * {@inheritdoc}
     */
    public function setFrom($fromAddress)
    {
        if ($this->useParent) {
            return parent::setFrom($fromAddress);
        }
        $this->zendMessage->setFrom($fromAddress);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addTo($toAddress)
    {
        if ($this->useParent) {
            return parent::addTo($toAddress);
        }
        $this->zendMessage->addTo($toAddress);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addCc($ccAddress)
    {
        if ($this->useParent) {
            return parent::addCc($ccAddress);
        }
        $this->zendMessage->addCc($ccAddress);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addBcc($bccAddress)
    {
        if ($this->useParent) {
            return parent::addBcc($bccAddress);
        }
        $this->zendMessage->addBcc($bccAddress);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setReplyTo($replyToAddress)
    {
        if ($this->useParent) {
            return parent::setReplyTo($replyToAddress);
        }
        $this->zendMessage->setReplyTo($replyToAddress);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getRawMessage()
    {
        if ($this->useParent) {
            return parent::getRawMessage();
        }

        return $this->zendMessage->toString();
    }
}
