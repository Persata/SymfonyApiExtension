## CHANGELOG

### v0.2.6 2018-02-01

- Added a new step definition for attaching collections of files:
```
Given the file "picture-1.png" is attached to the request as part of the "pictures" array
```

### v0.2.5 2018-01-29

- Fixed issue with duplicate step definitions

### v0.2.4 2018-01-29

- Added new assertions for checking the keys of nested JSON data:
```
Then the JSON response should contain the nested key "data.user.firstName"
# OR
Then the JSON response should contain the nested key "data:user:firstName" with the delimiter ":"
```

### v0.2.3 2018-01-29

- Added new assertions for checking the values of nested JSON data:
```
Then the JSON response should have the nested key "data.user.firstName" equal to "Alice"
# OR
Then the JSON response should have the nested key "data:user:firstName" equal to "Alice" with delimiter ":"
```

### v0.2.2 2018-01-26

- Moved the `assertJsonStructure` method from ApiContext to RawApiContext to allow child classes to use it

### v0.2.1 2018-01-04

- Fixed the functionality of the `base_url` setting for the extension.

### v0.2.0 2017-12-18

- Added support for file uploads in a similar fashion to Mink / Symfony's WebTestCase.
- Reworded the step "the request query parameters are" to "the request parameters are" to more accurately match Symfony's Request expectations. For GET requests, this step can
be used to set what the query parameters are, and for POST requests it can be used to set any form data in the request - query parameters for POST requests should now be added
as part of the URI instead.
