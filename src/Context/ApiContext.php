<?php

namespace Persata\SymfonyApiExtension\Context;

use Behat\Gherkin\Node\PyStringNode;
use Webmozart\Assert\Assert;

/**
 * Class ApiContext
 *
 * @package Persata\SymfonyApiExtension\Context
 */
class ApiContext extends RawApiContext
{
    /**
     * @Given /^the "([^"]*)" server parameter is "([^"]*)"$/
     */
    public function theServerParameter(string $key, string $value)
    {
        $this->getApiClient()->setServerParameter($key, $value);
    }

    /**
     * @Given /^the "([^"]*)" request header is "([^"]*)"$/
     */
    public function theRequestHeaderIs(string $key, string $value)
    {
        $this->getApiClient()->setRequestHeader($key, $value);
    }

    /**
     * @Given /^the request content type is JSON$/
     */
    public function theRequestContentTypeIsJSON()
    {
        $this->theRequestHeaderIs('Content-Type', 'application/json');
    }

    /**
     * @Given /^the request content type is XML/
     */
    public function theRequestContentTypeIsXML()
    {
        $this->theRequestHeaderIs('Content-Type', 'application/xml');
    }

    /**
     * @Given /^the request body is$/
     */
    public function theRequestBodyIs(PyStringNode $requestBody)
    {
        $this->getApiClient()->setRequestBody($requestBody);
    }

    /**
     * @Given /^the request parameters are$/
     */
    public function theRequestParametersAre(PyStringNode $requestParameterString)
    {
        $this->getApiClient()->setParameters(json_decode($requestParameterString->getRaw(), true));
    }

    /**
     * @Given /^the file "([^"]*)" is attached to the request with key "([^"]*)"$/
     */
    public function theFileIsAttachedToTheRequestWithKey(string $path, string $requestKey)
    {
        if ($this->getApiExtensionParameter('files_path')) {
            $fullPath = rtrim(realpath($this->getApiExtensionParameter('files_path')), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $path;
            if (is_file($fullPath)) {
                $path = $fullPath;
            }
        }

        $this->getApiClient()->addFile($path, $requestKey);
    }

    /**
     * @Given /^the file "([^"]*)" is attached to the request as part of the "([^"]*)" array$/
     */
    public function theFileIsAttachedToTheRequestAsPartOfTheArray(string $path, string $requestKey)
    {
        if ($this->getApiExtensionParameter('files_path')) {
            $fullPath = rtrim(realpath($this->getApiExtensionParameter('files_path')), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $path;
            if (is_file($fullPath)) {
                $path = $fullPath;
            }
        }

        $this->getApiClient()->addFileToArray($path, $requestKey);
    }

    /**
     * @When /^the request is sent using (GET|POST|PUT|PATCH|DELETE|OPTIONS) to "([^"]*)"$/
     */
    public function theRequestIsSentTo(string $method, string $uri)
    {
        $this->getApiClient()->request($method, $uri);
    }

    /**
     * @Then /^the response status code should be (\d+)$/
     */
    public function theResponseStatusCodeShouldBe(int $statusCode)
    {
        Assert::same($this->getApiClient()->getResponse()->getStatusCode(), $statusCode);
    }

    /**
     * @Then /^the "([^"]*)" response header is "([^"]*)"$/
     */
    public function theResponseHeaderIs(string $key, string $value)
    {
        Assert::same($this->getApiClient()->getResponse()->headers->get($key), $value);
    }

    /**
     * @Then /^the response content type should be HTML$/
     */
    public function theResponseContentTypeShouldBeHTML()
    {
        $this->theResponseContentTypeShouldStartWith('text/html');
    }

    /**
     * @Then /^the response content type should be JSON$/
     */
    public function theResponseContentTypeShouldBeJSON()
    {
        $this->theResponseContentTypeShouldStartWith('application/json');
    }

    /**
     * @Then /^the response content type should be XML$/
     */
    public function theResponseContentTypeShouldBeXML()
    {
        Assert::regex($this->getApiClient()->getResponse()->headers->get('content-type'), '/^.+\/xml/');
    }

    /**
     * @Given /^the response content type should be "([^"]*)"$/
     */
    public function theResponseContentTypeShouldBe(string $contentType)
    {
        Assert::same($this->getApiClient()->getResponse()->headers->get('content-type'), $contentType);
    }

    /**
     * @Then /^the response content type should start with "([^"]*)"$/
     */
    public function theResponseContentTypeShouldStartWith(string $contentType)
    {
        Assert::startsWith($this->getApiClient()->getResponse()->headers->get('content-type'), $contentType);
    }

    /**
     * @Then /^the response content should be valid JSON$/
     */
    public function theResponseContentShouldBeValidJSON()
    {
        json_decode($this->getApiClient()->getResponse()->getContent());
        Assert::same(0, json_last_error());
    }

    /**
     * @Then /^the JSON response should contain the key "([^"]*)"$/
     */
    public function theJSONResponseShouldContainTheKey(string $key)
    {
        Assert::keyExists(json_decode($this->getApiClient()->getResponse()->getContent(), true), $key);
    }

    /**
     * @Then /^the JSON response should contain the nested key "([^"]*)"$/
     * @Then /^the JSON response should contain the nested key "([^"]*)" with the delimiter "([^"]*)"$/
     */
    public function theJSONResponseShouldContainTheNestedKey(string $key, string $delimiter = '.')
    {
        $jsonUnderSpecification = json_decode($this->getApiClient()->getResponse()->getContent(), true);

        $keysToTraverse = explode($delimiter, $key);

        foreach ($keysToTraverse as $keyToTraverse) {
            Assert::keyExists($jsonUnderSpecification, $keyToTraverse);
            $jsonUnderSpecification = $jsonUnderSpecification[$keyToTraverse];
        }
    }

    /**
     * @Then /^the JSON response should be$/
     */
    public function theJSONResponseShouldBe(PyStringNode $expectedContentStringNode)
    {
        $expectedJson = json_decode($expectedContentStringNode->getRaw(), true);
        $responseJson = json_decode($this->getApiClient()->getResponse()->getContent(), true);
        Assert::same($expectedJson, $responseJson);
    }

    /**
     * @Then /^the JSON response should have the key "([^"]*)" equal to "([^"]*)"$/
     */
    public function theJSONResponseShouldHaveTheKeyEqualTo(string $key, string $value)
    {
        $responseJson = json_decode($this->getApiClient()->getResponse()->getContent(), true);

        Assert::keyExists($responseJson, $key);
        Assert::same($responseJson[$key], $value);
    }

    /**
     * @Then /^the JSON response should have the nested key "([^"]*)" equal to "([^"]*)"$/
     * @Then /^the JSON response should have the nested key "([^"]*)" equal to "([^"]*)" with the delimiter "([^"]*)"$/
     */
    public function theJSONResponseShouldHaveTheNestedKeyEqualTo(string $key, string $value, string $delimiter = '.')
    {
        $jsonUnderSpecification = json_decode($this->getApiClient()->getResponse()->getContent(), true);

        $keysToTraverse = explode($delimiter, $key);

        foreach ($keysToTraverse as $keyToTraverse) {
            Assert::keyExists($jsonUnderSpecification, $keyToTraverse);
            $jsonUnderSpecification = $jsonUnderSpecification[$keyToTraverse];
        }

        Assert::same($jsonUnderSpecification, $value);
    }

    /**
     * @Then /^the JSON response should have (\d+) elements in the "([^"]*)" array$/
     * @param int    $elementCount Number of elements
     * @param string $arrayName    Name of array
     */
    public function theJSONResponseShouldHaveNumberElementsInArray($elementCount, $arrayName)
    {
        $responseJson = json_decode($this->getApiClient()->getResponse()->getContent(), true);
        $foundArray = $responseJson;
        $arrayName = explode('.', $arrayName);
        foreach ($arrayName as $name) {
            Assert::keyExists($foundArray, $name);
            $foundArray = $foundArray[$name];
        }
        Assert::eq(count($foundArray), $elementCount);
    }

    /**
     * Checks that response body contains JSON from PyString.
     *
     * Do not check that the response body /only/ contains the JSON from PyString,
     *
     * @param PyStringNode $jsonString
     *
     * @throws \RuntimeException
     *
     * @Then /^(?:the )?response should contain json$/
     */
    public function theResponseShouldContainJson(PyStringNode $jsonString)
    {
        $etalon = json_decode($jsonString->getRaw(), true);
        $actual = json_decode($this->getApiClient()->getResponse()->getContent(), true);
        if (null === $etalon) {
            throw new \RuntimeException(
                "Can not convert etalon to json:\n" . $jsonString->getRaw()
            );
        }
        if (null === $actual) {
            throw new \RuntimeException(
                "Can not convert actual to json:\n" . (string)$this->getApiClient()->getResponse()->getContent()
            );
        }
        Assert::greaterThanEq(count($actual), count($etalon));
        $this->checkDataForJson($etalon, $actual);
    }

    /**
     * @param array $etalon
     * @param array $actual
     */
    private function checkDataForJson($etalon, $actual)
    {
        foreach ($etalon as $key => $needle) {
            Assert::keyExists($actual, $key);
            if (is_array($etalon[$key])) {
                $this->checkDataForJson($etalon[$key], $actual[$key]);
            } else {
                Assert::same($etalon[$key], $actual[$key]);
            }
        }
    }

    /**
     * @Then /^the JSON response should have the structure$/
     */
    public function theJSONResponseShouldHaveTheStructure(PyStringNode $rawJsonStringNode)
    {
        if (strpos($rawJsonStringNode->getRaw(), '{') === 0) {
            trigger_error(
                'Passing raw JSON to the structure test is deprecated and will be removed in v1.0. Please pass a PHP array instead.',
                E_USER_DEPRECATED
            );
            $expectedJsonStructure = json_decode($rawJsonStringNode->getRaw(), true);
        } else {
            $expectedJsonStructure = eval(sprintf('return %s;', $rawJsonStringNode->getRaw()));
        }

        $responseJson = json_decode($this->getApiClient()->getResponse()->getContent(), true);

        $this->assertJsonStructure(
            $expectedJsonStructure,
            $responseJson
        );
    }

    /**
     * @Then /^the XML response root should have attribute "([^"]*)" equal to "([^"]*)"$/
     */
    public function theXmlResponseShouldHaveAttributeEqualTo(string $name, string $value)
    {
        $responseXml = new \SimpleXMLElement($this->getApiClient()->getResponse()->getContent());
        Assert::eq($value, (string)$responseXml->attributes()->$name);
    }

    /**
     * @Then /^the XML response should have the child "([^"]*)"$/
     * @Then /^the XML response should have the child "([^"]*)" equal to "([^"]*)"$/
     */
    public function theXmlResponseShouldHaveTheChildEqualTo(string $child, $value = null)
    {
        $responseXml = new \SimpleXMLElement($this->getApiClient()->getResponse()->getContent());

        if ($value === null) {
            Assert::notEmpty((string)$responseXml->$child);
        } else {
            Assert::eq($value, (string)$responseXml->$child);
        }
    }

    /**
     * @Then the XML response should have the child :arg1 with the attribute :arg2 equal to :arg3
     */
    public function theXmlResponseShouldHaveTheChildWithTheAttributeEqualTo(string $child, $attribute, $value)
    {
        $responseXml = new \SimpleXMLElement($this->getApiClient()->getResponse()->getContent());
        Assert::eq($value, (string)$responseXml->$child->attributes()->$attribute);
    }

    /**
     * @Then /^the XML response should be$/
     */
    public function theXMLResponseShouldBe(PyStringNode $expectedContentStringNode)
    {
        $expectedXml = (new \SimpleXMLElement($expectedContentStringNode->getRaw()))->asXML();
        $responseXml = (new \SimpleXMLElement($this->getApiClient()->getResponse()->getContent()))->asXML();

        Assert::notEq($expectedXml, false);
        Assert::notEq($responseXml, false);

        Assert::eq($expectedXml, $responseXml);
    }
}
