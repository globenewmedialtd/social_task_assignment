# Social Task Assignment
This module adds a new task content type to opensocial.

## Requirements
Please make sure you have the following modules installed and enabled:
- drupal/twig_field_value : Work with field_values and field_labels seperatley on TWIG Level
- drupal/webform : Needed for the submissions
- drupal/webform_views : Needed for the submissions

## How to install
Please install like any other modules.

## How to configure
After the module has been installed you have to activate the new group content "Task" at the content tab inside the desired group. Please also do not forget to set the permissions inside the permissions tab. Be aware that the local task for the tab only appears when the group content plugin is installed.

## Webform Integration
Please note Webform will be used to collect structured information. In order to update task assignments you have to add a Handler called "Update task assignment" to the desired webform. You also have to make sure that the enabled webforms are also available at the entity reference field "Content type: task / field_webform". They must be ticked! This module ships with a webform called "default". By default there is no handler activated, due to installation problems. Please add the mentioned webform handler and leave the settings. We recommend you "duplicate" the default webform, that will save you a lot of time to configure the webform. It is tricky to make it work.

Please do not forget to tick "update and view own webform submission" at the drupal permissions settings.

## Activity Stream / Notifications
There are several message templates used, preconfigured. BUG: At the moment email sending is not working through "Activities", you can use a VBO action on the management table for the task assignments. Due to a lack of proper hooks, you need to adjust the notification views. Please remove the "activity_notification_visibility_access" and add our new "social_task_assignment_activity_notification_visibility_access". This is needed because of the permission settings shipped with Activities.

You find it here -> /admin/structure/views/view/activity_stream_notifications

### Available message templates
- Create task in group
- An organizer/tutor added me to a task
- Activity on tasks I am organizing

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

## Blocks

There are several blocks available for that module. Here you can see how they will appear:

- views_exposed_filter_block:user_tasks-page_profile
- group_add_task_block / Group add task block
- social_task_assignment_vbo_add_assignee_block / Social Task Assignment VBO add assignee block
- social_task_assignment_vbo_manage_assignee_block / Social Task Assignment VBO manage assignee block
- views_block:attached_webform_submissions-block_1
- views_block__task_feedback_block_1
- views_block:task_show_webform-block_1
- views_block:upcoming_tasks-block_my_upcoming_tasks
- views_block:upcoming_tasks-upcoming_tasks_group


## Permissions
Please do not forget to tick the new task group content permissions and most important grant the authenticated user "view own webform submissions" and "update own webform submissions"







