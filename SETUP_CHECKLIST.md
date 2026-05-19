# ✅ Instructor Verification System - Setup Checklist

> Use this checklist to ensure everything is properly set up and tested.

---

## 🔧 Installation (5 minutes)

- [ ] **Pull/Download** all new files from this session
  - Check modified and new files in the implementation summary
  
- [ ] **Run Migrations**
  ```bash
  php artisan migrate
  ```
  Expected: No errors, tables created

- [ ] **Create Storage Link**
  ```bash
  php artisan storage:link
  ```
  Expected: Symbolic link created

- [ ] **Clear Cache**
  ```bash
  php artisan optimize:clear
  php artisan filament:clear-cache
  ```
  Expected: Cache cleared

- [ ] **Test Composer**
  ```bash
  composer dumpautoload
  ```
  Expected: Autoloader updated

---

## 🧪 Functional Testing (30 minutes)

### Test 1: Register as Student ✓
- [ ] Go to http://localhost:8000/register
- [ ] Select "Student" role
- [ ] Fill in basic info
- [ ] **Expected**: No instructor-specific fields shown
- [ ] Submit
- [ ] **Expected**: Redirected to dashboard

### Test 2: Register as Instructor ✓
- [ ] Go to http://localhost:8000/register
- [ ] Select "Instructor" role
- [ ] **Expected**: Instructor fields appear
- [ ] Fill all fields:
  - [ ] Name
  - [ ] Email
  - [ ] Password (confirm)
  - [ ] Bio
  - [ ] Experience
  - [ ] Qualification type
  - [ ] Institution
  - [ ] Year
  - [ ] Portfolio URL (optional)
- [ ] Upload certificate file
  - [ ] Click or drag-drop
  - [ ] Select PDF or image (under 5MB)
  - [ ] **Expected**: Filename shows
- [ ] Upload ID proof
  - [ ] Click or drag-drop
  - [ ] Select PDF or image (under 5MB)
  - [ ] **Expected**: Filename shows
- [ ] Check terms checkbox
- [ ] Submit
- [ ] **Expected**: Registration successful

### Test 3: Dashboard Alert ✓
- [ ] After registration, check instructor dashboard
- [ ] **Expected**: See "⏳ Verification Pending" alert
- [ ] **Expected**: Alert explains review process
- [ ] **Expected**: Cannot create courses yet (button disabled)

### Test 4: Files Uploaded ✓
- [ ] Check `storage/app/public/instructor-verification/`
- [ ] **Expected**: Find certificate file
- [ ] **Expected**: Find identity file
- [ ] **Expected**: Files have timestamps in names
- [ ] **Expected**: Files are readable

### Test 5: Database Records ✓
- [ ] Check `users` table
  - [ ] New user has `instructor_status = 'pending'`
- [ ] Check `instructor_verifications` table
  - [ ] New record with user_id
  - [ ] All fields populated correctly
  - [ ] Status = 'pending'

### Test 6: Admin Panel Access ✓
- [ ] Login as admin (if available)
- [ ] Go to admin dashboard
- [ ] Look for "User Management" → "Instructor Verifications"
- [ ] **Expected**: Resource appears in sidebar
- [ ] Click it
- [ ] **Expected**: See list of applications

### Test 7: Admin List View ✓
- [ ] In Instructor Verifications list
- [ ] **Expected**: See pending application
- [ ] **Expected**: Status badge shows "Pending"
- [ ] **Expected**: Columns show: Name, Email, Qualification, Status
- [ ] **Expected**: Can search by name
- [ ] **Expected**: Can filter by status

### Test 8: Admin Detail View ✓
- [ ] Click on the instructor application
- [ ] **Expected**: See all details displayed
- [ ] **Expected**: Certificate image viewable
- [ ] **Expected**: ID proof image viewable
- [ ] **Expected**: Portfolio URL is clickable
- [ ] **Expected**: Buttons available: View, Edit, Approve, Reject

### Test 9: Approve Application ✓
- [ ] Click "Approve" button
- [ ] **Expected**: Status changes to "Approved"
- [ ] **Expected**: See success notification
- [ ] Check `users` table
  - [ ] `instructor_status` changed to 'verified'
- [ ] Check `instructor_verifications` table
  - [ ] `status` = 'approved'
  - [ ] `reviewed_by` = admin user ID
  - [ ] `reviewed_at` = current timestamp

### Test 10: Instructor Sees Approval ✓
- [ ] Login as the approved instructor
- [ ] Go to dashboard
- [ ] **Expected**: See "✓ Verified Instructor" alert
- [ ] **Expected**: Success message displayed
- [ ] **Expected**: Can now create courses

### Test 11: Reject Application ✓
- [ ] Register another instructor
- [ ] In admin panel, click "Reject"
- [ ] **Expected**: Form appears for rejection reason
- [ ] Enter reason: "Certificate expired"
- [ ] Submit
- [ ] **Expected**: Status changes to "Rejected"
- [ ] **Expected**: Rejection reason saved
- [ ] Check `users` table
  - [ ] `instructor_status` = 'rejected'

### Test 12: Instructor Sees Rejection ✓
- [ ] Login as rejected instructor
- [ ] Go to dashboard
- [ ] **Expected**: See "✗ Verification Rejected" alert
- [ ] **Expected**: Rejection reason shown
- [ ] **Expected**: Cannot create courses

---

## 🔒 Security Testing (15 minutes)

### Test 13: File Upload Validation ✓
- [ ] Try uploading .exe file
  - [ ] **Expected**: Rejected with error message
- [ ] Try uploading 10MB file
  - [ ] **Expected**: Rejected with size error
- [ ] Try uploading .txt file
  - [ ] **Expected**: Rejected with format error

### Test 14: Course Creation Block ✓
- [ ] Login as unverified instructor
- [ ] Try accessing `/instructor/courses/create`
  - [ ] **Expected**: Either redirected or shown error
  - [ ] (If middleware applied)
- [ ] Login as verified instructor
- [ ] Access `/instructor/courses/create`
  - [ ] **Expected**: Can access form
  - [ ] **Expected**: Can create course

### Test 15: Form Validation ✓
- [ ] Try submitting without bio
  - [ ] **Expected**: Validation error
- [ ] Try submitting without files
  - [ ] **Expected**: Optional field, accepted
- [ ] Try invalid email
  - [ ] **Expected**: Email validation error
- [ ] Try password mismatch
  - [ ] **Expected**: Confirmation error

---

## 🎨 UI/UX Testing (10 minutes)

### Test 16: Mobile Responsiveness ✓
- [ ] Open register form on mobile device
- [ ] **Expected**: Form is readable and usable
- [ ] **Expected**: File upload zones are tapable
- [ ] **Expected**: No horizontal scrolling

### Test 17: Form Field Visibility ✓
- [ ] Select "Student" role
  - [ ] **Expected**: Instructor section hidden
- [ ] Select "Instructor" role
  - [ ] **Expected**: Instructor section visible
  - [ ] **Expected**: Smooth animation
  - [ ] **Expected**: All fields shown

### Test 18: File Upload UX ✓
- [ ] Hover over upload zone
  - [ ] **Expected**: Visual feedback (color change)
- [ ] Drag file over zone
  - [ ] **Expected**: Visual feedback (highlight)
- [ ] Drop file
  - [ ] **Expected**: Accepted visually
- [ ] View uploaded filename
  - [ ] **Expected**: Filename displayed

---

## 📊 Edge Cases (10 minutes)

### Test 19: Multiple Instructors ✓
- [ ] Register 2 instructors
- [ ] Admin approves one, rejects other
- [ ] **Expected**: Each has independent status
- [ ] **Expected**: Dashboards show correct alerts

### Test 20: Session Handling ✓
- [ ] Register as instructor
- [ ] Close browser
- [ ] Reopen and login
- [ ] **Expected**: Status persists
- [ ] **Expected**: Alert still shows

### Test 21: Multiple Rejections ✓
- [ ] Instructor is rejected once
- [ ] In database, can they reapply?
- [ ] **Expected**: System allows (or handles appropriately)

### Test 22: File Deletion ✓
- [ ] Delete certificate file from storage
- [ ] Admin tries to view application
- [ ] **Expected**: Graceful handling (doesn't crash)

---

## 🚀 Production Readiness (5 minutes)

### Test 23: Database Backup ✓
- [ ] Run `php artisan backup:run`
  - [ ] **Expected**: Backup includes new tables

### Test 24: Environment Variables ✓
- [ ] Check `.env` has all needed variables
  - [ ] Storage accessible
  - [ ] Database connected
  - [ ] Email working (if using)

### Test 25: Performance ✓
- [ ] List view loads quickly (< 1s)
- [ ] Detail view loads quickly (< 1s)
- [ ] File uploads complete reliably

### Test 26: Logging ✓
- [ ] Check `storage/logs/laravel.log`
- [ ] **Expected**: No errors during tests
- [ ] **Expected**: Error logs for intentional failures

---

## 📝 Documentation Review (5 minutes)

- [ ] Read QUICK_START.md
  - [ ] Understand setup steps
  
- [ ] Read INSTRUCTOR_VERIFICATION_GUIDE.md
  - [ ] Understand complete flow
  
- [ ] Read UI_REFERENCE.md
  - [ ] Understand visual flows
  
- [ ] Review this file
  - [ ] Confirm all tests passed

---

## 🎯 Go-Live Checklist (Before Production)

- [ ] All tests passed above
- [ ] Admin trained on approval process
- [ ] Documentation in place for users
- [ ] Email templates ready (if using)
- [ ] Backup strategy defined
- [ ] Monitoring configured
- [ ] Support process documented
- [ ] Rollback plan prepared

---

## 🐛 Troubleshooting

| Issue | Solution |
|-------|----------|
| Files not uploading | Check storage permissions: `chmod 775 storage/app/public` |
| Filament resource missing | Clear cache: `php artisan filament:clear-cache` |
| Images not showing | Run: `php artisan storage:link` |
| Database errors | Run: `php artisan migrate` |
| 500 errors | Check `storage/logs/laravel.log` |

---

## ✨ Sign-Off

- [ ] All tests completed successfully
- [ ] No critical issues found
- [ ] Documentation reviewed
- [ ] Ready for user testing
- [ ] Ready for production

**Date Completed**: _______________

**Tested By**: _______________

**Sign-Off**: _______________

---

## 📞 Next Steps After Sign-Off

1. Train admin users
2. Communicate feature to instructors
3. Monitor early registrations
4. Gather feedback
5. Plan future enhancements

---

**System is ready for launch! 🚀**
