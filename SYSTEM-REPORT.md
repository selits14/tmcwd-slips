# TMCWD Slips Management System
## System Documentation Report

**Prepared for:** TMCWD Management  
**System:** TMCWD Slips Management System  
**Report date:** October 23, 2026  
**Status:** Demonstration deployment

## 1. Executive summary

The TMCWD Slips Management System is a web application for preparing and tracking employee pass slips, vehicle gate passes, and gas slips. It provides role-based access, approval and movement workflows, printable forms, and operational reports.

The system has been deployed to Render as a demonstration service using a PostgreSQL database. The current hosting plan is free and intended for demonstration. Its service may sleep when idle, and the database is temporary; it should not be used to hold official records without an approved production hosting and backup arrangement.

## 2. Purpose and scope

The system is intended to centralize the creation, approval, recording, printing, and reporting of three document types:

- **Employee pass slip:** records the employee, purpose, destination, and expected travel times.
- **Vehicle gate pass:** records the requester, driver, vehicle, destination, passengers, purpose, departure, and arrival.
- **Gas slip:** records the requesting driver, vehicle, fuel details, trip information, and fuel use.

The application also manages staff accounts, divisions, division heads, and vehicle records. It provides a dashboard and reports for slip activity and vehicle fuel/travel records.

## 3. Users and access controls

Public self-registration is disabled. A super administrator creates and manages staff accounts and assigns their roles, positions, divisions, and active status.

| User type | Main responsibilities |
|---|---|
| Super administrator | Manage accounts, divisions, and vehicles; create and view slips; approve/reject slips; access reports. |
| Encoder | Create slips and view system slip records; access reports. The encoder is recorded separately from the requester. |
| General Manager | Approve/reject slips across divisions. |
| Division head / Division Manager | Approve/reject slips within the applicable division. Division head assignments are configured on division records. |
| Driver | View associated slips and complete trip information for assigned gas slips. |
| Guard | View pass slips and vehicle gate passes; record employee time-out/time-in and vehicle release/return. |
| Viewer | Read slip records and reports without creation or approval authority. |

Approval checks are enforced by the application. Approval is restricted to the super administrator, General Manager, or authorized division manager/head. The generic `approver` role alone does not grant approval authority; the user's position or division-head assignment is also checked. Slip lists and individual record access are filtered according to the user's role and relationship to the slip.

## 4. Operational workflows

### 4.1 Employee pass slip

An encoder or super administrator creates a pass slip for an active employee. The slip begins pending review. An authorized manager approves or rejects it. Once approved, a guard can record the employee leaving and returning. Slip status and verification details are retained for review and printing.

### 4.2 Vehicle gate pass

An encoder or super administrator records the requester and vehicle trip details. The pass requires approval before a guard can record vehicle release. The guard records the vehicle's return, completing the movement record.

### 4.3 Gas slip

An encoder or super administrator selects an active vehicle and driver and enters the fuel request. The selected driver is recorded as the requester, while the signed-in user is recorded as encoder. After approval, the assigned driver records trip times, distance, and fuel used. Fuel accounting prevents the recorded fuel used from exceeding the available starting balance, issued fuel, and fuel purchased during the trip.

## 5. System functions

- Sequential slip numbering with a year-based display prefix, such as `GP-2026-00001` and `GS-2026-0001`. The underlying sequence continues across calendar years.
- Search and filtering of slip records by available fields such as status, date, requester/driver, vehicle, and document number.
- Individual and batch PDF printing.
- Reports for pass slips, vehicle gate passes, fuel consumption, vehicle MROT, and vehicle RFC.
- Dashboard showing pending counts and recent activity within the signed-in user's visibility.
- Audit-related fields recording requester, encoder, approver/rejector, and movement or trip completion actions where applicable.
- State checks that prevent a slip from being advanced from an unexpected status.

## 6. Deployment status and limitations

The demonstration deployment is hosted at:

**https://tmcwd-slips-demo.onrender.com/**

The current Render configuration uses a Docker web service and PostgreSQL. Migrations are applied when the container starts. Demo administrator creation is controlled through deployment environment variables and should be disabled after the initial account is created.

The free hosting setup has operational limitations:

- The web service may sleep when idle, causing a delay on the first request.
- The free database is temporary and may expire.
- The deployment does not yet provide an organization-approved backup and recovery service level.
- It is suitable for demonstration and evaluation, not for permanent storage of official slips.

Use sample data only until production hosting, security review, backup, and recovery arrangements are approved.

## 7. Security and data handling

The system requires sign-in for operational pages, disables public registration, restricts administrative pages to super administrators, and checks slip visibility and approval permissions on the server. Passwords are stored using Laravel's password hashing. Deployment secrets such as the application key and database credentials are configured outside source control.

Before production use, the organization should confirm its account lifecycle process, password reset email delivery, hosting access controls, backup retention, restoration procedures, and applicable records retention requirements. The current Render demo should not contain confidential or official operational records.

## 8. Recommended next steps

1. **Approve a production hosting approach.** Select a supported host with a persistent database and organization-controlled access.
2. **Set up backups and recovery.** Define backup frequency, retention, responsible personnel, and restoration testing.
3. **Review roles and divisions with management.** Confirm who may encode, approve, view, and record movements for each division.
4. **Conduct user acceptance testing.** Use representative staff accounts and sample slips to validate each workflow and printed form.
5. **Configure email and operational monitoring.** Ensure password reset delivery and define how deployment and database issues will be monitored.
6. **Approve data handling.** Establish what information may be entered, who can access it, and how long records must be retained.

## 9. Conclusion

The system provides a working foundation for managing TMCWD employee, vehicle, and fuel slips with controlled approval and recordkeeping workflows. The current deployment is useful for demonstrations and stakeholder feedback. Production use should follow only after hosting, access policy, backup/recovery, and operational ownership are agreed upon.

---

*This report describes the current application implementation and its demonstration deployment. It is not a formal security certification or production-readiness approval.*
