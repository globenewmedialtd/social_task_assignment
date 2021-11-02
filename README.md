# Social Task Assignment
This module adds a new task content type to opensocial.

## How to install
Please install like any other modules.

## How to configure
After the module has been installed you have to activate the new group content "Task" at the content tab inside the desired group. Please also do not forget to set the permissions inside the permissions tab. 

## Webform Integration
Please note Webform will be used to collect structured information. In order to update task assignments you have to add a Handler called "Update task assignment" to the desired webform. You also have to make sure that the enabled webforms are also available at the entity reference field "Content type: task / field_webform". They must be ticked! This module ships with a webform called "default". By default there is no handler activated, due to installation problems. Please add the mentioned webform handler and leave the settings. We recommend you "duplicate" the default webform, that will save you a lot of time to configure the webform. It is tricky to make it work.

Please do not forget to tick "update and view own webform submission" at the drupal permissions settings.

## Activity Stream / Notifications
There are several message templates used, preconfigured. BUG: At the moment email sending is not working through "Activities", you can use a VBO action on the management table for the task assignments. Due to a lack of proper hooks, you need to adjust the notification views. Please remove the "activity_notification_visibility_access" and add our new "social_task_assignment_activity_notification_visibility_access". This is needed because of the permission settings shipped with Activities.

## Task Assignment & VBO
If you create your first task, you will see that all group members will be around shortly and are waiting for you to assign. BUG: Exposed filter issue.
If you delete a group member also the task assignment will be deleted. If you add one or more members, they automatically will be available for you to assign.

### Available actions 

Available when you visit your task management view

- Export Task Assignees
- Send email to assignees
- Unassign assignees

Available when you click on "Add assignees"
- Assign group members


## Permissions
Please do not forget to tick the new task group content permissions and most important grant the authenticated user "view own webform submissions" and "update own webform submissions"







