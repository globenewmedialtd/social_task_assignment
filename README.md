# Social Task Assignment
This module adds a new task content type and a tasks tab to the group menu.

## How to install
Please install like any other modules.

## How to configure
After the module has been installed you have to activate the new group content "Task" at the content tab inside the desired group. Please also do not forget to set the permissions inside the permissions tab.

## Webform Integration
Please note Webform will be used to collect structured information. In order to update task assignments you have to add a Handler called "Update task assignment" to the desired webform. You also have to make sure that the enabled webforms are also available at the entity reference field "Content type: task / field_webform". They must be ticked! 

Inside the entity "Task assignment / field_webform_submissions" you also have to make sure to tick the allowed webforms.

## Permissions
Please do not forget to tick the new task group content permissions and most important grant the authenticated user "view own webform submissions" and "update own webform submissions"





