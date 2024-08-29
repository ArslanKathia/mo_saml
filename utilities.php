<?php
// This file is part of miniOrange moodle plugin - http://moodle.org/
//
// This Plugin is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// This Program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * @package   auth_mo_saml
 * @copyright   2020  miniOrange
 * @category    document
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL v3 or later, see license.txt
 */

include_once 'xmlseclibs.php';
use \RobRichards\XMLSecLibs\XMLSecurityKey;
use \RobRichards\XMLSecLibs\XMLSecurityDSig;
use \RobRichards\XMLSecLibs\XMLSecEnc;

//require_once('../../config.php');

/**
 * Auth external functions
 *
 * @package    mo_saml
 * @category   utilities
 * @copyright  2020 miniOrange
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class utilities {

    public static function generate_id() {
        return '_' . self::string_to_hex(self::generate_random_bytes(21));
    }

    public static function string_to_hex($bytes) {
        $ret = '';
        for ($i = 0; $i < strlen($bytes); $i++) {
            $ret .= sprintf('%02x', ord($bytes[$i]));
        }
        return $ret;
    }

    public static function generate_random_bytes($length, $fallback = true) {
        return openssl_random_pseudo_bytes($length);
    }

    public static function generate_timestamp($instant = null) {
        if ($instant === null) {
            $instant = time();
        }
        return gmdate('Y-m-d\TH:i:s\Z', $instant);
    }

    public static function xs_date_time_to_timestamp($time) {
        $matches = array();

        // We use a very strict regex to parse the timestamp.
        $regex = '/^(\\d\\d\\d\\d)-(\\d\\d)-(\\d\\d)T(\\d\\d):(\\d\\d):(\\d\\d)(?:\\.\\d+)?Z$/D';
        if (preg_match($regex, $time, $matches) == 0) {
            echo sprintf('Invalid SAML2 timestamp passed to xs_date_time_to_timestamp: '.$time);
            exit;
        }

        // Extract the different components of the time from the  matches in the regex.
        // Intval will ignore leading zeroes in the string.
        $year   = intval($matches[1]);
        $month  = intval($matches[2]);
        $day    = intval($matches[3]);
        $hour   = intval($matches[4]);
        $minute = intval($matches[5]);
        $second = intval($matches[6]);

        // We use gmmktime because the timestamp will always be given in UTC.
        $ts = gmmktime($hour, $minute, $second, $month, $day, $year);

        return $ts;
    }

    public static function extract_strings(DOMElement $parent, $namespaceuri, $localname) {

        $ret = array();
        for ($node = $parent->firstChild; $node !== null; $node = $node->nextSibling) {
            if ($node->namespaceURI !== $namespaceuri || $node->localName !== $localname) {
                continue;
            }
            $ret[] = trim($node->textContent);
        }

        return $ret;
    }


    public static function xpquery($node, $query) {
        static $xpcache = null;

        if ($node instanceof DOMDocument) {
            $doc = $node;
        } else {
            $doc = $node->ownerDocument;
        }

        if ($xpcache === null || !$xpcache->document->isSameNode($doc)) {
            $xpcache = new DOMXPath($doc);
            $xpcache->registerNamespace('soap-env', 'http://schemas.xmlsoap.org/soap/envelope/');
            $xpcache->registerNamespace('saml_protocol', 'urn:oasis:names:tc:SAML:2.0:protocol');
            $xpcache->registerNamespace('saml_assertion', 'urn:oasis:names:tc:SAML:2.0:assertion');
            $xpcache->registerNamespace('saml_metadata', 'urn:oasis:names:tc:SAML:2.0:metadata');
            $xpcache->registerNamespace('ds', 'http://www.w3.org/2000/09/xmldsig#');
            $xpcache->registerNamespace('xenc', 'http://www.w3.org/2001/04/xmlenc#');
        }

        $results = $xpcache->query($query, $node);
        $ret = array();
        for ($i = 0; $i < $results->length; $i++) {
            $ret[$i] = $results->item($i);
        }

        return $ret;
    }


    public static function validate_element(DOMElement $root) {

        // Create an XML security object.
        $objxmlsecdsig = new XMLSecurityDSig();

        // Both SAML messages and SAML assertions use the 'ID' attribute.
        $objxmlsecdsig->idKeys[] = 'ID';
        // Locate the XMLDSig Signature element to be used.
        $signatureelement = self::xpquery($root, './ds:Signature');
        if (count($signatureelement) === 0) {
            // We don't have a signature element to validate.
            return false;
        } else if (count($signatureelement) > 1) {
            echo sprintf('XMLSec: more than one signature element in root.');
            exit;
        }
        // Removed code.
        $signatureelement = $signatureelement[0];
        $objxmlsecdsig->sigNode = $signatureelement;

        // Canonicalize the XMLDSig SignedInfo element in the message.
        $objxmlsecdsig->canonicalizeSignedInfo();

        // Validate referenced xml nodes.
        if (!$objxmlsecdsig->validateReference()) {
            echo sprintf('XMLsec: digest validation failed');
            exit;
        }

        // Check that $root is one of the signed nodes.
        $rootsigned = false;
        foreach ($objxmlsecdsig->getValidatedNodes() as $signednode) {
            if ($signednode->isSameNode($root)) {
                $rootsigned = true;
                break;
            } else if ($root->parentNode instanceof DOMDocument && $signednode->isSameNode($root->ownerDocument)) {
                // $root is the root element of a signed document.
                $rootsigned = true;
                break;
            }
        }

        if (!$rootsigned) {
            echo sprintf('XMLSec: The root element is not signed.');
            exit;
        }

        // Now we extract all available X509 certificates in the signature element.
        $certificates = array();
        foreach (self::xpquery($signatureelement, './ds:KeyInfo/ds:X509Data/ds:X509Certificate') as $certnode) {
            $certdata = trim($certnode->textContent);
            $certdata = str_replace(array("\r", "\n", "\t", ' '), '', $certdata);
            $certificates[] = $certdata;
        }

        $ret = array(
            'Signature' => $objxmlsecdsig,
            'Certificates' => $certificates,
            );
        return $ret;
    }

    public static function parse_name_id(DOMElement $xml) {
        $ret = array('Value' => trim($xml->textContent));

        foreach (array('NameQualifier', 'SPNameQualifier', 'Format') as $attr) {
            if ($xml->hasAttribute($attr)) {
                $ret[$attr] = $xml->getAttribute($attr);
            }
        }

        return $ret;
    }

    public static function process_response($currenturl, $certfingerprint, $signaturedata, saml_response_class $response) {

        $assertion = current($response->get_assertions());

        $notbefore = $assertion->get_not_before(); 
        if ($notbefore !== null && $notbefore > time() + 60) {
            die('Received an assertion that is valid in the future. Check clock synchronization on IdP and SP.');
        }

        $notonorafter = $assertion->get_not_onor_after();
        if ($notonorafter !== null && $notonorafter <= time() - 60) {
            die('Received an assertion that has expired. Check clock synchronization on IdP and SP.');
        }

        $sessionnotonorafter = $assertion->get_session_not_onor_after();
        if ($sessionnotonorafter !== null && $sessionnotonorafter <= time() - 60) {
            die('Received an assertion with a session that has expired. Check clock synchronization on IdP and SP.');
        }

        // Validate Response-element destination.
        $msgdestination = $response->get_destination();
        if (substr($msgdestination, -1) == '/') {
            $msgdestination = substr($msgdestination, 0, -1);
        }
        if (substr($currenturl, -1) == '/') {
            $currenturl = substr($currenturl, 0, -1);
        }

        if ($msgdestination !== null && $msgdestination !== $currenturl) {
            echo sprintf('Destination in response doesn\'t match the current URL. Destination is "' .
                $msgdestination . '", current URL is "' . $currenturl . '".');
            exit;
        }

        $responsesigned = self::check_sign($certfingerprint, $signaturedata);

        // Returning boolean $responsesigned.
        return $responsesigned;
    }

    public static function validate_signature(array $info, XMLSecurityKey $key) {

        /** @var XMLSecurityDSig $objxmlsecdsig */
        $objxmlsecdsig = $info['Signature'];

        $sigmethod = self::xpquery($objxmlsecdsig->sigNode, './ds:SignedInfo/ds:SignatureMethod');
        if (empty($sigmethod)) {
            echo sprintf('Missing SignatureMethod element');
            exit();
        }
        $sigmethod = $sigmethod[0];
        if (!$sigmethod->hasAttribute('Algorithm')) {
            echo sprintf('Missing Algorithm-attribute on SignatureMethod element.');
            exit;
        }
        $algo = $sigmethod->getAttribute('Algorithm');

        if ($key->type === XMLSecurityKey::RSA_SHA1 && $algo !== $key->type) {
            $key = self::cast_key($key, $algo);
        }

        // Check the signature.
        if (! $objxmlsecdsig->verify($key)) {
            echo sprintf('Unable to validate Sgnature');
            exit;
        }
    }

    public static function cast_key(XMLSecurityKey $key, $algorithm, $type = 'public') {

        // Do nothing if algorithm is already the type of the key.
        if ($key->type === $algorithm) {
            return $key;
        }

        $keyinfo = openssl_pkey_get_details($key->key);
        if ($keyinfo === false) {
            echo sprintf('Unable to get key details from xml_security_key.');
            exit;
        }
        if (!isset($keyinfo['key'])) {
            echo sprintf('Missing key in public key details.');
            exit;
        }

        $newkey = new XMLSecurityKey($algorithm, array('type' => $type));
        $newkey->loadKey($keyinfo['key']);
        return $newkey;
    }

    public static function sanitize_certificate( $certificate ) {
        $certificate = preg_replace("/[\r\n]+/", '', $certificate);
        $certificate = str_replace( "-", '', $certificate );
        $certificate = str_replace( "BEGIN CERTIFICATE", '', $certificate );
        $certificate = str_replace( "END CERTIFICATE", '', $certificate );
        $certificate = str_replace( " ", '', $certificate );
        $certificate = chunk_split($certificate, 64, "\r\n");
        $certificate = "-----BEGIN CERTIFICATE-----\r\n" . $certificate . "-----END CERTIFICATE-----";
        return $certificate;
    }

    public static function check_sign($certfingerprint, $signaturedata) {
        $certificates = $signaturedata['Certificates'];

        if (count($certificates) === 0) {
            return false;
        }

        $fparray = array();
        $fparray[] = $certfingerprint;
        $pemcert = self::find_certificate($fparray, $certificates);

        $lastexception = null;
        $key = new XMLSecurityKey(XMLSecurityKey::RSA_SHA1, array('type' => 'public'));
        $key->loadKey($pemcert);

        try {
            /*
             * Make sure that we have a valid signature
             */
            self::validate_signature($signaturedata, $key);
            return true;
        } catch (Exception $e) {
            $lastexception = $e;
        }
        // We were unable to validate the signature with any of our keys.
        if ($lastexception !== null) {
            throw $lastexception;
        } else {
            return false;
        }

    }

    public static function validate_issuer_and_audience($samlresponse, $spentityid, $issuertovalidateagainst) {
        $issuer = current($samlresponse->get_assertions())->get_issuer();
        $assertion = current($samlresponse->get_assertions());
        $audiences = $assertion->get_valid_audiences();
        if (strcmp($issuertovalidateagainst, $issuer) === 0) {
            if (!empty($audiences)) {
                if (in_array($spentityid, $audiences, true)) {
                    return true;
                } else {
                    echo sprintf('Invalid Audience URI. Expected one of the Audiences to be: '. $spentityid);
                    exit;
                }
            }
        } else {
            echo sprintf('Issuer cannot be verified.');
            exit;
        }
    }

    private static function find_certificate(array $certfingerprints, array $certificates) {

        $candidates = array();

        foreach ($certificates as $cert) {
            $fp = strtolower(sha1(base64_decode($cert)));
            if (!in_array($fp, $certfingerprints, true)) {
                $candidates[] = $fp;
                continue;
            }

            // We have found a matching fingerprint.
            $pem = "-----BEGIN CERTIFICATE-----\n" .
                chunk_split($cert, 64) .
                "-----END CERTIFICATE-----\n";

            return $pem;
        }

        echo sprintf('Unable to find a certificate matching the configured fingerprint.');
        exit;
    }

}