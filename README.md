# Fake WordPress User Generator
This script allows you to create 100 fake users in WordPress site for testing purposes.
This overrides wp_mail() function so no emails should go out during the user creation.
This is because triggering 100 emails for a very short period of time may trigger some anti-spam filters. 

If this is executed on https://qSandbox.com the outgoing emails block is already taken care of for you.

Blog post: https://qSandbox.com/1083

You need to upload it where WordPress config is and access it from the browser example.com/fake-user-gen.php.
Everytime you run this it will create another set of users. Make sure you delete this after the testing is done.

Requirements:
- the hosting to support php exec() function
- wp-cli to be installed and available as 'wp'

# Customizations
For a customization feel free to reach us to get a quote at https://qSandbox.com/contact

# Author
Copyright: Svetoslav Marinov | https://qSandbox.com
