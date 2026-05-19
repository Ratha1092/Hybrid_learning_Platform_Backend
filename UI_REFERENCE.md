# Instructor Verification System - UI Reference

## 📝 Registration Form Flow

### When "Student" is Selected
```
┌─────────────────────────────┐
│  I want to join as          │
│  ○ Student                  │
│  ○ Instructor               │
└─────────────────────────────┘
│ Name                        │
│ [Your full name]            │
└─────────────────────────────┘
│ Email                       │
│ [you@example.com]           │
└─────────────────────────────┘
│ Password                    │
│ [••••••••]                  │
└─────────────────────────────┘
│ Confirm Password            │
│ [••••••••]                  │
└─────────────────────────────┘
│ ☑ I agree to terms          │
└─────────────────────────────┘
│   CREATE ACCOUNT →          │
└─────────────────────────────┘
```

### When "Instructor" is Selected
```
┌─────────────────────────────┐
│  I want to join as          │
│  ○ Student                  │
│  ● Instructor               │
└─────────────────────────────┘
│ Name                        │
│ [Your full name]            │
└─────────────────────────────┘
│ Email                       │
│ [you@example.com]           │
└─────────────────────────────┘
│ Password                    │
│ [••••••••]                  │
└─────────────────────────────┘
│ Confirm Password            │
│ [••••••••]                  │
└─────────────────────────────┘

┌──────── INSTRUCTOR FIELDS ────────┐
│ ⓘ Please provide evidence...      │
│                                   │
│ Professional Bio                  │
│ [Tell us about your teaching...] │
│                                   │
│ Teaching Experience               │
│ [Describe your years...      ]   │
│                                   │
│ Qualification Type                │
│ [-- Select a qualification --]    │
│  ├─ Degree                        │
│  ├─ Certification                 │
│  └─ Professional Experience       │
│                                   │
│ Institution / Organization        │
│ [University or org name]          │
│                                   │
│ Year of Completion                │
│ [2023]                            │
│                                   │
│ Portfolio / Website (Optional)    │
│ [https://yourportfolio.com]       │
│                                   │
│ Certificate / Credential          │
│ ┌───────────────────────────────┐ │
│ │ 📄 Click or drag & drop       │ │
│ │ PDF, JPG, PNG (Max 5MB)       │ │
│ │ ✓ certificate.pdf             │ │
│ └───────────────────────────────┘ │
│                                   │
│ ID Proof / Document               │
│ ┌───────────────────────────────┐ │
│ │ 🪪 Click or drag & drop       │ │
│ │ PDF, JPG, PNG (Max 5MB)       │ │
│ │ ✓ id_scan.jpg                 │ │
│ └───────────────────────────────┘ │
│                                   │
│ ┌───────────────────────────────┐ │
│ │ ✓ Your verification documents │ │
│ │ will be reviewed within 24-48 │ │
│ │ hours. You'll receive an      │ │
│ │ email notification.           │ │
│ └───────────────────────────────┘ │
└──────────────────────────────────┘

│ ☑ I agree to terms               │
└─────────────────────────────────┘
│   CREATE ACCOUNT →              │
└─────────────────────────────────┘
```

---

## 📊 Instructor Dashboard - Status Alerts

### ⏳ Pending Verification
```
┌────────────────────────────────────────────┐
│ ⏳ Verification Pending                    │
│                                            │
│ Your instructor verification is being      │
│ reviewed by our admin team. You'll         │
│ receive an email when approved. In the     │
│ meantime, you cannot create courses.       │
└────────────────────────────────────────────┘
```

### ✗ Verification Rejected
```
┌────────────────────────────────────────────┐
│ ✗ Verification Rejected                    │
│                                            │
│ Your instructor verification was           │
│ rejected. Reason: The documents provided   │
│ do not meet our qualification standards.   │
└────────────────────────────────────────────┘
```

### ✓ Verified Instructor
```
┌────────────────────────────────────────────┐
│ ✓ Verified Instructor                      │
│                                            │
│ You are verified! You can now create and   │
│ publish courses.                           │
└────────────────────────────────────────────┘
```

---

## 🔍 Admin Panel - Filament Interface

### List View
```
Instructor Verifications

Filter: [Pending ▼] [Search...]

┌─────────────────────────────────────────────┐
│ Instructor    │ Email      │ Qual. │ Status │
├─────────────────────────────────────────────┤
│ Sarah Johnson │ sarah@... │ Deg. │ ⏳ Pen.│
│ John Smith    │ john@...  │ Cert │ ⏳ Pen.│
│ Alex Brown    │ alex@...  │ Prof │ ✓ App. │
└─────────────────────────────────────────────┘
```

### Detail View
```
Instructor Verification - Sarah Johnson

Applicant Information
├─ Name: Sarah Johnson
└─ Email: sarah.johnson@example.com

Professional Details
├─ Bio: 10+ years teaching experience...
├─ Experience: Taught Python, JavaScript...
├─ Qualification: Degree
├─ Institution: MIT
├─ Completion Year: 2016
└─ Portfolio: https://sarahjohnson.dev

Documents
├─ Certificate: [IMAGE]
└─ ID Proof: [IMAGE]

Review Status
├─ Status: Pending Review
├─ Reviewed By: -
└─ Reviewed At: -

[✓ Approve]  [✗ Reject]  [Edit]  [Delete]
```

### Approve Action
```
[✓ Approve Button]
↓
Set Status: approved
Record: reviewed_by = Admin ID
        reviewed_at = now()
Update User: instructor_status = 'verified'
↓
✓ Confirmation: "Instructor Approved"
"Sarah Johnson has been approved as an instructor."
```

### Reject Action
```
[✗ Reject Button]
↓
Shows Form:
┌──────────────────────────────┐
│ Rejection Reason             │
│ [Enter reason for rejection] │
│                              │
│ Example: The certificate     │
│ provided is expired.         │
│                              │
│ [Submit]  [Cancel]           │
└──────────────────────────────┘
↓
Set Status: rejected
Record: reviewed_by = Admin ID
        reviewed_at = now()
        rejection_reason = "..."
Update User: instructor_status = 'rejected'
↓
✓ Confirmation: "Application Rejected"
```

---

## 🗂️ File Upload Storage Structure

```
storage/app/public/
└── instructor-verification/
    ├── certificates/
    │   ├── 2026_04_23_user_1_certificate.pdf
    │   ├── 2026_04_23_user_2_diploma.jpg
    │   └── 2026_04_23_user_3_cert.png
    └── identity/
        ├── 2026_04_23_user_1_id.pdf
        ├── 2026_04_23_user_2_passport.jpg
        └── 2026_04_23_user_3_license.png
```

---

## 🔄 Complete User Journey

### Day 1: Instructor Registration
```
User visits /register
      ↓
Selects "Instructor"
      ↓
Fills registration form
      ↓
Uploads certificate & ID
      ↓
Submits
      ↓
Redirected to /instructor/dashboard
      ↓
Sees: "⏳ Verification Pending"
      ↓
Cannot create courses yet
      ↓
Receives email: "Thank you, we're reviewing..."
```

### Day 2-3: Admin Reviews
```
Admin goes to /admin/dashboard
      ↓
Clicks: User Management → Instructor Verifications
      ↓
Sees list of pending applications
      ↓
Clicks on instructor's name
      ↓
Views full details & documents
      ↓
Decides: Approve ✓
      ↓
Clicks [✓ Approve]
      ↓
System updates:
  - Status → "approved"
  - User.instructor_status → "verified"
```

### Day 4: Instructor Notified
```
Instructor logs in
      ↓
Dashboard now shows: "✓ Verified Instructor"
      ↓
Can now click "New Course"
      ↓
Starts creating course
      ↓
Course goes live
```

---

## 📱 Responsive Design Notes

- ✅ Registration form: Optimized for mobile with single-column layout
- ✅ File upload zones: Touch-friendly with large tap targets
- ✅ Admin panel (Filament): Responsive table with mobile navigation
- ✅ Dashboard alerts: Full-width on mobile, padded on desktop

---

## 🎨 Color Codes Used

| State | Color | Icon |
|-------|-------|------|
| Pending | Gold/Yellow | ⏳ |
| Approved/Verified | Green | ✓ |
| Rejected | Red | ✗ |
| Info | Blue | ⓘ |
| Warning | Orange | ⚠️ |

---

## ⌚ Timeline

| Action | Time |
|--------|------|
| Instructor registers | Immediate |
| Appears in admin queue | Immediate |
| Admin reviews | 24-48 hours |
| Instructor notified | Minutes after review |
| Can create courses | Immediately after approval |

---

## 🔐 Permission Requirements

| User Type | Can Access |
|-----------|-----------|
| Student | ✓ Register as student |
| Guest Instructor | ✓ Register with evidence |
| Pending Instructor | ✗ Cannot create courses |
| Verified Instructor | ✓ Create courses |
| Admin | ✓ Review all applications |
| Super Admin | ✓ Full control |

---

## 🧪 Test Scenarios

### Scenario 1: Happy Path
- Register as instructor
- Admin approves
- Create course
- Expected: Success

### Scenario 2: Rejection Path
- Register as instructor  
- Admin rejects with reason
- See rejection on dashboard
- Cannot create courses
- Expected: Clear feedback

### Scenario 3: File Validation
- Try uploading .exe file
- System rejects
- Try uploading 10MB file
- System rejects
- Expected: Proper error messages

### Scenario 4: Multiple Applications
- Two instructors apply
- Admin approves both
- Both can create courses
- Expected: Isolation and independence
