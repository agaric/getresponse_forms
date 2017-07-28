Create blocks for subscribing to GetResponse lists anywhere on your
Drupal site. This module requires the [GetResponse module](http://www.drupal.org/project/getresponse).

## Installation

1. Enable the GetResponse Forms module and the Entity Module

2. If you haven't done so already, add a list in your MailChimp account. You can
follow these directions provided by MailChimp on how to 
[add or import a list](http://kb.mailchimp.com/article/how-do-i-create-and-import-my-list).

## Usage
To create a signup form:
* Go to admin/config/services/mailchimp/signup
* Click the "Add a signup form" button
* Complete the form
* You may need to clear caches to make your blocks and pages appear

Merge Fields
You will see Merge Field options based on the configuration of your list through
MailChimp. You can expose these merge fields to the end user to complete. These
fields are automatically set to "required" or "optional" based on Mailchimp's
merge field settings.
