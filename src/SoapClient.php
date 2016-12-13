<?php
/**
 * Contains \JamesIArmes\PhpNtlm\NTLMSoapClient.
 */

namespace jamesiarmes\PhpNtlm;

/**
 * Soap Client using Microsoft's NTLM Authentication.
 *
 * @package php-ntlm\Soap
 */
class SoapClient extends \SoapClient
{
    /**
     * cURL resource used to make the SOAP request
     *
     * @var resource
     */
    protected $ch;

    /**
     * Options passed to the client constructor.
     *
     * @var array
     */
    protected $options;

    /**
     * {@inheritdoc}
     */
    public function __construct($wsdl, array $options = null)
    {
        // Set missing indexes to their default value.
        $options += array(
            'user' => null,
            'password' => null,
            'curlopts' => array(),
        );
        $this->options = $options;

        // Verify that a user name and password were entered.
        if (empty($options['user']) || empty($options['password'])) {
            throw new \BadMethodCallException(
                'A username and password is required.'
            );
        }

        parent::__construct($wsdl, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function __doRequest($request, $location, $action, $version, $one_way = 0)
    {
        $headers = $this->buildHeaders($action);
        $this->__last_request = $request;
        $this->__last_request_headers = $headers;

        // Only reinitialize curl handle if the location is different.
        if (!$this->ch
            || curl_getinfo($this->ch, CURLINFO_EFFECTIVE_URL) != $location) {
            $this->ch = curl_init($location);
        }

        curl_setopt_array($this->ch, $this->curlOptions($action, $request));
        $response = curl_exec($this->ch);

        // TODO: Add some real error handling.
        // If the response if false than there was an error and we should throw
        // an exception.
        if ($response === false) {
            $this->__last_response = $this->__last_response_headers = false;
            throw new \RuntimeException(
                'Curl error: ' . curl_error($this->ch),
                curl_errno($this->ch)
            );
        }

        // Parse the response and set the last response and headers.
        $info = curl_getinfo($this->ch);
        $this->__last_response_headers = substr($response, 0, $info['header_size']);
        $this->__last_response = substr($response, $info['header_size']);

        return $this->__last_response;
    }

    /**
     * {@inheritdoc}
     */
    public function __getLastRequestHeaders()
    {
        return implode("\n", $this->__last_request_headers) . "\n";
    }

    /**
     * Returns the response code from the last request
     *
     * @return integer
     *
     * @throws \BadMethodCallException
     *   If no cURL resource has been initialized.
     */
    public function getResponseCode()
    {
        if (empty($this->ch)) {
            throw new \BadMethodCallException('No cURL resource has been '
                . 'initialized. This is probably because no request has not '
                . 'been made.');
        }

        return curl_getinfo($this->ch, CURLINFO_HTTP_CODE);
    }

    /**
     * Builds the headers for the request.
     *
     * @param string $action
     *   The SOAP action to be performed.
     */
    protected function buildHeaders($action)
    {
        return array(
            'Method: POST',
            'Connection: Keep-Alive',
            'User-Agent: PHP-SOAP-CURL',
            'Content-Type: text/xml; charset=utf-8',
            "SOAPAction: \"$action\"",
            'Expect: 100-continue',
        );
    }

    /**
     * Builds an array of curl options for the request
     *
     * @param string $action
     *   The SOAP action to be performed.
     * @param string $request
     *   The XML SOAP request.
     * @return array
     *   Array of curl options.
     */
    protected function curlOptions($action, $request)
    {
        $options = $this->options['curlopts'] + array(
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $this->buildHeaders($action),
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_HTTPAUTH => CURLAUTH_BASIC | CURLAUTH_NTLM,
            CURLOPT_USERPWD => $this->options['user'] . ':'
                               . $this->options['password'],
        );

        // We shouldn't allow these options to be overridden.
        $options[CURLOPT_HEADER] = true;
        $options[CURLOPT_POST] = true;
        $options[CURLOPT_POSTFIELDS] = $request;

        return $options;
    }
}
