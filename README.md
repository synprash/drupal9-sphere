# drupal9-sphere

Assignment:

1. Create a content type "entries" with fields title, description, select list (with option Animals, Anime, Anti-Malware). -> *Added config of content type in custom module assignment*
2. Use the open API https://api.publicapis.org/entries -> *added configuration to store this api endpoint in custom module*
3. Create a page to show the API output based on the select list. (Category in the API output should match the select list)  -> *Created custom block and placed it below main content.You can find  block redenr code in custom module 'assignment'*
5. Display the output in a tabular form  -> *Refer above point*
6. Create a service and use dependency injection.  ->  *added  prior services & dependacy injections in custom module*
7. The link (from the API output) should open in a popup. - *Yes added the custom render logic n table + custom library to open the  file in Ppop up.*
8. Create a custom block and display the output from https://api.coindesk.com/v1/bpi/currentprice.json API. - *Created another custom block 'BitcoinBlock' to show bitcoin prices.*
9. Create a POST API, input body parameter nid and title, The title should be updated. * @TODO - did not get enough time . will finish this later*
10. All the configuration should be part of the module so when we install/uninstall the module the config should get imported/removed respectively. -> *Yes all configuration added in custom module*
