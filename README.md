# WPCitizenReporter

CitizenReporter is a set of tools that help newsrooms and freelance journalists improve the way they collect and manage breaking news from the field.

This is the newsroom dashboard component. The dashboard is a web-based management interface that editors use to set assignments, manage reporters, edit incoming content from the field, and pay reporters for their work.

For a detailed documentation on how it works check out the [wiki page here](https://github.com/CodeForAfrica/CitizenReporter/wiki)

## Deployment

Citizen Reporter is based on Wordpress and you just need to install this plugin to get started. Here is how to go abou it:

1. Install Wordpress(if you don't already have a running instance). 

Here's a guide on [how to install WordPress](https://codex.wordpress.org/Installing_WordPress)

2. Install the Citizen Reporter plugin
 
Simply clone this repository to your /wp-content/plugins folder. Or download it as a zip file and extract to the said folder.

3. Install dependencies
 
    * We have built the messaging component as an [independent plugin](https://github.com/nickhargreaves/WP_GCM_Chat) so that others can use it in their projects. You can it install the same way you installed the main plugin in 2 above.
    * You also need to install the [JSON-API plugin](https://wordpress.org/plugins/json-api/)

4. Themeing

You can use your own theme for the dashboard or leave the default Wordpress admin theme. However we recommend using the [WP Flat Admin theme](https://github.com/nickhargreaves/WP_FlatAdmin) that we built to work with this plugin.