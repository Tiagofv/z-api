<?php

namespace Tiagofv\ZApi;

use GuzzleHttp\Client;

class ZApi
{

    /**
     * @var string
     */
    private $instanceId;
    /**
     * @var string
     */
    private $tokenId;

    /**
     * @var string
     */
    private $baseUrl;

    /**
     * @var array
     */
    private $allowedHttpVerbs = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE'];

    /**
     * @var array
     */
    private $allowedExtensions = ['pdf', 'docx', 'doc', 'xlsx', 'deb', 'gz', '7z', 'zip'];

    /**
     * ZApi constructor.
     * @param string $instanceId
     * @param string $tokenId
     */
    public function __construct(string $instanceId, string $tokenId)
    {
        $this->instanceId = $instanceId;
        $this->tokenId = $tokenId;
        $this->baseUrl = "https://api.z-api.io/instances/{$instanceId}/token/{$tokenId}/";
    }

    /**
     * Creates a guzzle instance
     * @return Client
     */
    private function getClient(array $formData = []): Client
    {
        $client = new Client([
            'base_uri' => $this->baseUrl,
            'headers' => [
                'content-type' => 'application/json',
            ],
            'form_params' => $formData,
        ]);

        return $client;
    }

    public function doRequest(string $method, string $url, array $data)
    {
        $method = strtoupper($method);
        $allowedHttpVerbs = $this->allowedHttpVerbs;
        if (! in_array($method, $allowedHttpVerbs)) {
            throw new \InvalidArgumentException(
                "The supplied http method is invalid. Allowed values are " .
                implode(',', $allowedHttpVerbs) . '. Supplied value: ' . $method
            );
        }

        $response = $this->getClient($data)->request($method, $url);

        return  json_decode($response->getBody()->getContents());
    }

    // Instance related

    /**
     * Generates the qr code returns in bytes
     */
    public function generateQrCode()
    {
        return $this->doRequest('GET', 'qr-code', []);
    }

    public function generateQrCodeBase64()
    {
        return $this->doRequest('GET', 'qr-code/image', []);
    }

    /**
     * restarts the instance
     */
    public function restartInstance()
    {
        return $this->doRequest('GET', 'qr-code/image', []);
    }

    /**
     * disconnect the instance
     */
    public function disconnect()
    {
        return $this->doRequest('GET', 'disconnect', []);
    }

    /**
     * get the instance status
     */
    public function status()
    {
        return $this->doRequest('GET', 'status', []);
    }

    /**
     * get the instance status
     */
    public function restoreSession()
    {
        return $this->doRequest('GET', 'restore-session', []);
    }

    //    end instance related

    // message related

    /**
     * Sends a text message
     *
     * @param string $phone e.g: 5527995556362
     * @param string $message e.g: Aloha message!
     * @return string
     */
    public function sendText(string $phone, string $message)
    {
        return $this->doRequest('POST', 'send-text', [
            'phone' => $phone,
            'message' => $message,
        ]);
    }

    /**
     * Sends a contact to a phone number.
     * @param string $phone e.g: 5527995556362
     * @param string $contactName e.g: Rogério
     * @param string $contactPhone e.g: 5527995556362
     * @param string $contactBusinessDescription e.g: "Speak to one of our people"
     * @return string
     */
    public function sendContact(string $phone, string $contactName, string $contactPhone, string $contactBusinessDescription)
    {
        return $this->doRequest('POST', 'send-contact', [
            'phone' => $phone,
            'contactName' => $contactName,
            'contactPhone' => $contactPhone,
            'contactBusinessDescription' => $contactBusinessDescription,
        ]);
    }

    /**
     * Sends a image from URL
     * @param string $phone e.g: 5527995556362
     * @param string $imageUrl e.g: https://picsum.photos/200/300/
     * @return string
     */
    public function sendImageFromUrl(string $phone, string $imageUrl)
    {
        return $this->doRequest('POST', 'send-image', [
            'phone' => $phone,
            'image' => $imageUrl,
        ]);
    }

    /**
     * Sends audio from URL
     * @param string $phone e.g: 5527995556362
     * @param string audioUrl e.g: https://picsum.photos/200/300/
     * @return string
     */
    public function sendAudioFromUrl(string $phone, string $audioUrl)
    {
        return $this->doRequest('POST', 'send-audio', [
            'phone' => $phone,
            'audio' => $audioUrl,
        ]);
    }

    /**
     * Sends video from URL
     * @param string $phone e.g: 5527995556362
     * @param string $videoUrl e.g: http://techslides.com/demos/sample-videos/small.mp4
     * @return string
     */
    public function sendVideoFromUrl(string $phone, string $videoUrl)
    {
        return $this->doRequest('POST', 'send-video', [
            'phone' => $phone,
            'video' => $videoUrl,
        ]);
    }

    /**
     * Sends document from URL
     * @param string $phone e.g: 5527995556362
     * @param string $documentUrl e.g: https://expoforest.com.br/wp-content/uploads/2017/05/exemplo.pdf
     * @param string $extension e.g: pdf
     * @return string
     */
    public function sendDocumentFromUrl(string $phone, string $documentUrl, string $extension)
    {
        if (! in_array($extension, $this->allowedExtensions)) {
            throw new \InvalidArgumentException('Invalid extension supplied for extension parameter. It should be one of ' .
                implode(',', $this->allowedExtensions)
                . '. Supplied: ' . $extension);
        }

        return $this->doRequest('POST', 'send-document/' . $extension, [
            'phone' => $phone,
            'document' => $documentUrl,
        ]);
    }

    /**
     * Sends a link
     * @param string $phone e.g: 5527995556362
     * @param string $message text about the website
     * @param string $imageUrl image url
     * @param string $linkUrl link url
     * @param string $title title
     * @param string $linkDescription description
     * @return string
     */
    public function sendLink(string $phone, string $message, string $imageUrl, string $linkUrl, string $title, string $linkDescription)
    {
        return $this->doRequest('POST', 'send-link', [
            'phone' => $phone,
            'message' => $message . ' ' . $linkUrl,
            'image' => $imageUrl,
            'linkUrl' => $linkUrl,
            'title' => $title,
            'linkDescription' => $linkDescription,
        ]);
    }

    /**
     * Reads the message.
     *
     * @param string $phone
     * @param string $messageId
     * @return void
     */
    public function readMessage(string $phone, string $messageId)
    {
        return $this->doRequest('POST', 'read-message', [
            'phone' => $phone,
            'messageId' => $messageId,
        ]);
    }

    /**
     * delete a message.
     *
     * @param string $phone
     * @param string $messageId
     * @param bool $owner true if is the owner of the message
     * @return void
     */
    public function deleteMessage(string $phone, string $messageId, bool $owner)
    {
        return $this->doRequest('POST', 'read-message', [
            'phone' => $phone,
            'messageId' => $messageId,
            'owner' => $owner,
        ]);
    }

    // end message related


    // Status related!

    /**
     * Send a text status
     * @param string $message
     * @return string
     */
    public function sendTextStatus(string $message)
    {
        return $this->doRequest('POST', 'send-text-status', [
            'message' => $message,
        ]);
    }

    /**
     * Send a image status
     * @param string $message
     * @param string $imageB64
     * @return string
     */
    public function sendImageStatus($imageB64)
    {
        return $this->doRequest('POST', 'send-text-status', [
            'image' => $imageB64,
        ]);
    }

    // End status related

    // start chat related

    /**
     * Get all chats of the instance
     * @return string
     */
    public function getChats()
    {
        return $this->doRequest('GET', 'chats', []);
    }

    /**
     * Get all chats of the instance created by the provided phone
     * @param string $phone
     * @return string
     */
    public function getChatByPhone(string $phone)
    {
        return $this->doRequest('GET', 'chats/' . $phone, []);
    }

    /**
     * Get all chats of the instance created by the provided phone
     * @param string $chatPhone
     * @return string
     */
    public function getChatMessages(string $chatPhone)
    {
        return $this->doRequest('GET', 'chat-messages/' . $chatPhone, []);
    }

    // end chat related

    // contact related

    /**
     * Get contacts
     * @param int $page
     * @param int $pageSize
     * @return string
     */
    public function getContacts(int $page = 1, int $pageSize = 15)
    {
        return $this->doRequest('GET', "contacts?page={$page}&pageSize={$pageSize}", []);
    }

    /**
     * Get contacts by phone
     * @param string $phone
     * @return string
     */
    public function getContactsByPhone(string $phone)
    {
        return $this->doRequest('GET', "contacts/{$phone}", []);
    }

    /**
     * Get profile picture by number
     * @param string $phone
     * @return string
     */
    public function getProfilePictureByNumber(string $phone)
    {
        return $this->doRequest('GET', "profile-picture?phone={$phone}", []);
    }

    /**
     * Verifies if a number  is on whatsapp
     * @param string $phone
     * @return string
     */
    public function numberExistsOnWhatsapp(string $phone)
    {
        return $this->doRequest('GET', "phone-exists/{$phone}", []);
    }

    //end contact related
}
