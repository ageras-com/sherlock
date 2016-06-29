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


	Copyright (c) 2016: Ageras Aps and other contributors:
	
	Permission is hereby granted, free of charge, to any person 
	obtaining a copy of this software and associated documentation 
	files (the "Software"), to deal in the Software without restriction, 
	including without limitation the rights to use, copy, modify, merge,
	publish, distribute, sublicense, and/or sell copies of the Software, 
	and to permit persons to whom the Software is furnished to do so, 
	subject to the following conditions:
	
	The above copyright notice and this permission notice shall be included 
	in all copies or substantial portions of the Software.
	
	THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS 
	OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, 
	FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL 
	THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR 
	OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, 
	ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR 
	OTHER DEALINGS IN THE SOFTWARE.
