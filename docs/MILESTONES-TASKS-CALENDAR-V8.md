# TUFF BEATZ — Milestones, Tasks & Production Calendar V8

Status: IMPLEMENTED

V8 adds production operations management directly to each TUFF BEATZ project workspace.

## Default production timeline

Every project can initialize with these milestones:

1. Deposit Paid
2. Stems / Source Files Due
3. Production
4. Mix V1
5. Client Review
6. Mix Approval
7. Mastering
8. Final Delivery

TUFF BEATZ can change each milestone status, assign a project participant, set a due date, and add custom tasks/milestones from the WordPress request editor.

## Statuses

- Pending
- In Progress
- Waiting
- Completed
- Blocked

Overdue tasks are visually flagged when their due date passes while they remain incomplete.

## Project Dashboard

Authorized project members get a new Timeline section showing:

- Overall completion percentage
- Milestone/task status
- Due dates
- Assignees
- Task notes
- Overdue state
- Activity history

Project owners and TUFF BEATZ admins can complete or reopen milestones directly from the workspace.

## Activity timeline

V8 stores up to 200 recent project events in `_tb_activity_v8`. The timeline records milestone changes and is also connected to major existing workflows such as secure file uploads, review mix uploads, revision requests, mix approval and project messages.

## Permissions

All authorized project members may view the timeline. Milestone completion controls are limited to the project owner and TUFF BEATZ administrators. Assignment options include the project owner and project collaborators already managed by V5.

## Files

- `tuff-beatz/inc/project-milestones.php`
- `tuff-beatz/inc/project-dashboard.php`
- `tuff-beatz/assets/css/milestones.css`

## Baseline protection

V8 is isolated to the authenticated project-management workflow. The approved V3.4 public homepage and its visual baseline are not redesigned by this feature.
