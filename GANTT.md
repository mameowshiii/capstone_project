# Barangay Pili System - Gantt Chart

```mermaid
gantt
    title Barangay Pili Online Services Portal
    dateFormat  YYYY-MM-DD
    axisFormat  %b %d

    section Planning
    Project Planning       :done, planning, 2026-06-01, 14d
    Requirements Gathering :done, req, 2026-06-15, 14d

    section Design
    System Design          :design, 2026-07-01, 21d
    UI/UX Design           :ui, 2026-07-08, 21d
    Database Design        :db, 2026-07-15, 14d

    section Development
    Resident Module        :dev1, 2026-08-01, 21d
    Admin Module           :dev2, 2026-08-15, 21d
    Certificate Processing :dev3, 2026-08-29, 21d

    section Testing
    System Testing         :test, 2026-09-15, 14d
    Bug Fixing             :bugs, 2026-09-22, 14d

    section Deployment
    Deployment             :deploy, 2026-10-01, 7d
    Final Documentation    :docs, 2026-10-01, 14d
