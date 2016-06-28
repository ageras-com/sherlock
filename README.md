# Ageras Sherlock
[![Latest Stable Version](https://poser.pugx.org/ageras/sherlock/v/stable)](https://packagist.org/packages/ageras/sherlock)
[![Total Downloads](https://poser.pugx.org/ageras/sherlock/downloads)](https://packagist.org/packages/ageras/sherlock)
[![Monthly Downloads](https://poser.pugx.org/ageras/sherlock/d/monthly)](https://packagist.org/packages/ageras/sherlock)
[![Latest Unstable Version](https://poser.pugx.org/ageras/sherlock/v/unstable)](https://packagist.org/packages/ageras/sherlock)
[![License](https://poser.pugx.org/ageras/sherlock/license)](https://packagist.org/packages/ageras/sherlock)

## Description
Integrations to lookup companies.

## Requirements
General requirements.

### Packages
    "guzzlehttp/guzzle": "^6.2",
    "vlucas/phpdotenv": "^2.2"

### Danish Provider
Danish service provider uses virk.dk elastic search for validation and getting information about a company. 
This credentials needs to be obtained from the proper authorities. [virkdata](https://www.gitbook.com/book/virkdata/open-data-school/details)

    COMPANY_SERVICE_CVR_USERNAME=
    COMPANY_SERVICE_CVR_PASSWORD=

## Contributing

### Bug Reports
All issues are welcome, to create a better product, but your issue should contain a title and a clear description of the issue. You should also include as much relevant information as possible and a code sample that demonstrates the issue.

### Which Branch?
All bug fixes should be sent to the develop branch. Bug fixes should never be sent to the master

### Security Vulnerabilities
If you discover a security vulnerability within Sherlock package, write an email to Ageras' development team.

### Coding Style
Ageras' follows the PSR-2 coding standard and the [PSR-4](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-4-autoloader.md) autoloading standard.

### StyleCI
 StyleCI automatically fixes code style to match the standard.

## TODO

- [x] Danish Provider
- [ ] Swedish Provider
- [ ] Norwegian Provider
- [ ] Dutch Provider
- [ ] German Provider

## License


	Copyright 2016 Ageras Aps

	Licensed under the Apache License, Version 2.0 (the "License");
	you may not use this file except in compliance with the License.
	You may obtain a copy of the License at

	   http://www.apache.org/licenses/LICENSE-2.0

	Unless required by applicable law or agreed to in writing, software
	distributed under the License is distributed on an "AS IS" BASIS,
	WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
	See the License for the specific language governing permissions and
	limitations under the License.
