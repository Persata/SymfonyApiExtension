<?php

namespace Persata\SymfonyApiExtension\Context;

use Webmozart\Assert\Assert;

/**
 * Class FOSRestFormValidationContext
 *
 * @package Persata\SymfonyApiExtension\Context
 */
class FOSRestFormValidationContext extends RawApiContext
{
    /**
     * @Then /^the JSON response should have the error "([^"]*)" at "([^"]*)"$/
     * @Then /^the JSON response should have the error '([^']*)' at "([^"]*)"$/
     */
    public function theJSONResponseShouldHaveTheErrorAt(string $errorMessage, string $formChildFieldDescriptor)
    {
        $child = $this->getChildField($formChildFieldDescriptor);
        Assert::oneOf($errorMessage, $child['errors']);
    }

    /**
     * @Then /^the JSON response should not have any errors on "([^"]*)"$/
     */
    public function theJSONResponseShouldNotHaveAnyErrorsOn(string $formChildFieldDescriptor)
    {
        Assert::keyNotExists($this->getChildField($formChildFieldDescriptor), 'errors');
    }

    /**
     * @param string $formChildFieldDescriptor
     * @return array
     */
    protected function getChildField(string $formChildFieldDescriptor)
    {
        $responseJson = json_decode($this->getApiClient()->getResponse()->getContent(), true);
        $fields = explode('.', $formChildFieldDescriptor);
        $currentChild = $responseJson['errors'];

        foreach ($fields as $field) {
            $currentChild = $currentChild['children'][$field];
        }

        return $currentChild;
    }
}