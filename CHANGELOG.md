## CHANGELOG

### v0.2.0 2017-12-18

- Added support for file uploads in a similar fashion to Mink / Symfony's WebTestCase.
- Reworded the step "the request query parameters are" to "the request parameters are" to more accurately match Symfony's Request expectations. For GET requests, this step can
be used to set what the query parameters are, and for POST requests it can be used to set any form data in the request - query parameters for POST requests should now be added
as part of the URI instead.
