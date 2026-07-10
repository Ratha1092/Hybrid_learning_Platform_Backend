# Instructions

- Following Playwright test failed.
- Explain why, be concise, respect Playwright best practices.
- Provide a snippet of code with the fix, if possible.

# Test info

- Name: sidebar.spec.ts >> Sidebar – group collapse and expand >> "Reports" group collapses and expands
- Location: tests/playwright/sidebar.spec.ts:235:9

# Error details

```
Test timeout of 30000ms exceeded.
```

```
Error: locator.scrollIntoViewIfNeeded: Test timeout of 30000ms exceeded.
Call log:
  - waiting for locator('.fi-sidebar-group[data-group-label="Reports"]').locator('.fi-sidebar-group-btn').first()

```

# Page snapshot

```yaml
- generic [active] [ref=e1]:
  - navigation [ref=e3]:
    - generic [ref=e5]:
      - generic [ref=e6]: hybrid-learning
      - generic [ref=e7]: /
      - generic [ref=e8]: Update
    - generic [ref=e9]:
      - link "Commission 20%" [ref=e10] [cursor=pointer]:
        - /url: http://localhost:8000/admin/settings#finance
        - img [ref=e11]
        - text: Commission 20%
      - generic [ref=e15]:
        - generic [ref=e16]: Global search
        - generic [ref=e17]:
          - img [ref=e19]
          - searchbox "Global search" [ref=e22]
      - button [ref=e24] [cursor=pointer]:
        - img [ref=e25]
      - button "User menu" [ref=e29] [cursor=pointer]:
        - img "Avatar of Super Admin" [ref=e30]
  - generic [ref=e31]:
    - complementary [ref=e33]:
      - generic [ref=e36]:
        - link "Hybrid Learning admin local" [ref=e37] [cursor=pointer]:
          - /url: http://localhost:8000/admin
          - img [ref=e39]
          - generic [ref=e42]:
            - generic [ref=e43]: Hybrid Learning
            - generic [ref=e44]:
              - generic [ref=e45]: admin
              - generic [ref=e47]: local
        - button "Collapse sidebar" [ref=e48] [cursor=pointer]:
          - img [ref=e49]
      - navigation [ref=e51]:
        - list [ref=e52]:
          - listitem [ref=e53]:
            - list [ref=e54]:
              - listitem [ref=e55]:
                - link "Dashboard" [ref=e56] [cursor=pointer]:
                  - /url: http://localhost:8000/admin
                  - img [ref=e57]
                  - generic [ref=e59]: Dashboard
          - listitem [ref=e60]:
            - generic [ref=e61] [cursor=pointer]:
              - generic [ref=e62]: Business Intelligence
              - button "Business Intelligence" [expanded] [ref=e63]:
                - img [ref=e64]
            - list [ref=e66]:
              - listitem [ref=e67]:
                - link "Executive Center" [ref=e68] [cursor=pointer]:
                  - /url: http://localhost:8000/admin/bi/executive
                  - img [ref=e69]
                  - generic [ref=e71]: Executive Center
              - listitem [ref=e72]:
                - link "Revenue Intelligence" [ref=e73] [cursor=pointer]:
                  - /url: http://localhost:8000/admin/bi/revenue
                  - img [ref=e74]
                  - generic [ref=e76]: Revenue Intelligence
              - listitem [ref=e77]:
                - link "Marketplace Intelligence" [ref=e78] [cursor=pointer]:
                  - /url: http://localhost:8000/admin/bi/marketplace
                  - img [ref=e79]
                  - generic [ref=e81]: Marketplace Intelligence
              - listitem [ref=e82]:
                - link "Student Intelligence" [ref=e83] [cursor=pointer]:
                  - /url: http://localhost:8000/admin/bi/students
                  - img [ref=e84]
                  - generic [ref=e86]: Student Intelligence
              - listitem [ref=e87]:
                - link "Instructor Intelligence" [ref=e88] [cursor=pointer]:
                  - /url: http://localhost:8000/admin/bi/instructors
                  - img [ref=e89]
                  - generic [ref=e91]: Instructor Intelligence
              - listitem [ref=e92]:
                - link "Course Intelligence" [ref=e93] [cursor=pointer]:
                  - /url: http://localhost:8000/admin/bi/courses
                  - img [ref=e94]
                  - generic [ref=e96]: Course Intelligence
              - listitem [ref=e97]:
                - link "Financial Intelligence" [ref=e98] [cursor=pointer]:
                  - /url: http://localhost:8000/admin/bi/financial
                  - img [ref=e99]
                  - generic [ref=e101]: Financial Intelligence
              - listitem [ref=e102]:
                - link "Operational Intelligence" [ref=e103] [cursor=pointer]:
                  - /url: http://localhost:8000/admin/bi/operations
                  - img [ref=e104]
                  - generic [ref=e107]: Operational Intelligence
          - listitem [ref=e108]:
            - generic [ref=e109] [cursor=pointer]:
              - generic [ref=e110]: Data Exports
              - button "Data Exports" [expanded] [ref=e111]:
                - img [ref=e112]
            - list [ref=e114]:
              - listitem [ref=e115]:
                - link "Revenue Report" [ref=e116] [cursor=pointer]:
                  - /url: http://localhost:8000/admin/reports/revenue
                  - img [ref=e117]
                  - generic [ref=e119]: Revenue Report
              - listitem [ref=e120]:
                - link "Payments Report" [ref=e121] [cursor=pointer]:
                  - /url: http://localhost:8000/admin/reports/payments
                  - img [ref=e122]
                  - generic [ref=e124]: Payments Report
              - listitem [ref=e125]:
                - link "Payouts Report" [ref=e126] [cursor=pointer]:
                  - /url: http://localhost:8000/admin/reports/payouts
                  - img [ref=e127]
                  - generic [ref=e129]: Payouts Report
              - listitem [ref=e130]:
                - link "Course Intelligence" [ref=e131] [cursor=pointer]:
                  - /url: http://localhost:8000/admin/reports/course-intelligence
                  - img [ref=e132]
                  - generic [ref=e134]: Course Intelligence
              - listitem [ref=e135]:
                - link "Instructor Intelligence" [ref=e136] [cursor=pointer]:
                  - /url: http://localhost:8000/admin/reports/instructor-intelligence
                  - img [ref=e137]
                  - generic [ref=e139]: Instructor Intelligence
              - listitem [ref=e140]:
                - link "Learning Intelligence" [ref=e141] [cursor=pointer]:
                  - /url: http://localhost:8000/admin/reports/learning-intelligence
                  - img [ref=e142]
                  - generic [ref=e144]: Learning Intelligence
              - listitem [ref=e145]:
                - link "User Report" [ref=e146] [cursor=pointer]:
                  - /url: http://localhost:8000/admin/reports/users
                  - img [ref=e147]
                  - generic [ref=e149]: User Report
          - listitem [ref=e150]:
            - generic [ref=e151] [cursor=pointer]:
              - generic [ref=e152]: Learning
              - button "Learning" [expanded] [ref=e153]:
                - img [ref=e154]
            - list [ref=e156]:
              - listitem [ref=e157]:
                - link "Categories" [ref=e158] [cursor=pointer]:
                  - /url: http://localhost:8000/admin/categories
                  - img [ref=e159]
                  - generic [ref=e162]: Categories
              - listitem [ref=e163]:
                - link "Courses" [ref=e164] [cursor=pointer]:
                  - /url: http://localhost:8000/admin/courses
                  - img [ref=e165]
                  - generic [ref=e167]: Courses
              - listitem [ref=e168]:
                - link "Sections" [ref=e169] [cursor=pointer]:
                  - /url: http://localhost:8000/admin/sections
                  - img [ref=e170]
                  - generic [ref=e172]: Sections
              - listitem [ref=e173]:
                - link "Lessons" [ref=e174] [cursor=pointer]:
                  - /url: http://localhost:8000/admin/lessons
                  - img [ref=e175]
                  - generic [ref=e178]: Lessons
              - listitem [ref=e179]:
                - link "Reviews" [ref=e180] [cursor=pointer]:
                  - /url: http://localhost:8000/admin/reviews
                  - img [ref=e181]
                  - generic [ref=e183]: Reviews
          - listitem [ref=e184]:
            - generic [ref=e185] [cursor=pointer]:
              - generic [ref=e186]: Commerce
              - button "Commerce" [expanded] [ref=e187]:
                - img [ref=e188]
            - list [ref=e190]:
              - listitem [ref=e191]:
                - link "Orders" [ref=e192] [cursor=pointer]:
                  - /url: http://localhost:8000/admin/orders
                  - img [ref=e193]
                  - generic [ref=e195]: Orders
              - listitem [ref=e196]:
                - link "Payments" [ref=e197] [cursor=pointer]:
                  - /url: http://localhost:8000/admin/payments
                  - img [ref=e198]
                  - generic [ref=e200]: Payments
              - listitem [ref=e201]:
                - link "Coupons" [ref=e202] [cursor=pointer]:
                  - /url: http://localhost:8000/admin/coupons
                  - img [ref=e203]
                  - generic [ref=e205]: Coupons
              - listitem [ref=e206]:
                - link "Refunds •" [ref=e207] [cursor=pointer]:
                  - /url: http://localhost:8000/admin/refunds
                  - img [ref=e208]
                  - generic [ref=e210]: Refunds
                  - generic [ref=e214]: •
          - listitem [ref=e215]:
            - generic [ref=e216] [cursor=pointer]:
              - generic [ref=e217]: People
              - button "People" [expanded] [ref=e218]:
                - img [ref=e219]
            - list [ref=e221]:
              - listitem [ref=e222]:
                - link "Users" [ref=e223] [cursor=pointer]:
                  - /url: http://localhost:8000/admin/users
                  - img [ref=e224]
                  - generic [ref=e226]: Users
              - listitem [ref=e227]:
                - link "Instructors" [ref=e228] [cursor=pointer]:
                  - /url: http://localhost:8000/admin/instructors
                  - img [ref=e229]
                  - generic [ref=e231]: Instructors
              - listitem [ref=e232]:
                - link "Verifications" [ref=e233] [cursor=pointer]:
                  - /url: http://localhost:8000/admin/instructor-verifications
                  - img [ref=e234]
                  - generic [ref=e236]: Verifications
          - listitem [ref=e237]:
            - generic [ref=e238] [cursor=pointer]:
              - generic [ref=e239]: Finance
              - button "Finance" [expanded] [ref=e240]:
                - img [ref=e241]
            - list [ref=e243]:
              - listitem [ref=e244]:
                - link "Payouts" [ref=e245] [cursor=pointer]:
                  - /url: http://localhost:8000/admin/payouts
                  - img [ref=e246]
                  - generic [ref=e248]: Payouts
              - listitem [ref=e249]:
                - link "Wallets" [ref=e250] [cursor=pointer]:
                  - /url: http://localhost:8000/admin/wallets
                  - img [ref=e251]
                  - generic [ref=e253]: Wallets
              - listitem [ref=e254]:
                - link "Invoices" [ref=e255] [cursor=pointer]:
                  - /url: http://localhost:8000/admin/invoices
                  - img [ref=e256]
                  - generic [ref=e258]: Invoices
              - listitem [ref=e259]:
                - link "Receipts" [ref=e260] [cursor=pointer]:
                  - /url: http://localhost:8000/admin/receipts
                  - img [ref=e261]
                  - generic [ref=e263]: Receipts
          - listitem [ref=e264]:
            - generic [ref=e265] [cursor=pointer]:
              - generic [ref=e266]: System
              - button "System" [expanded] [ref=e267]:
                - img [ref=e268]
            - list [ref=e270]:
              - listitem [ref=e271]:
                - link "Settings" [ref=e272] [cursor=pointer]:
                  - /url: http://localhost:8000/admin/settings
                  - img [ref=e273]
                  - generic [ref=e276]: Settings
              - listitem [ref=e277]:
                - link "Roles" [ref=e278] [cursor=pointer]:
                  - /url: http://localhost:8000/admin/roles
                  - img [ref=e279]
                  - generic [ref=e281]: Roles
              - listitem [ref=e282]:
                - link "Notifications" [ref=e283] [cursor=pointer]:
                  - /url: http://localhost:8000/admin/notifications
                  - img [ref=e284]
                  - generic [ref=e286]: Notifications
              - listitem [ref=e287]:
                - link "Moderation" [ref=e288] [cursor=pointer]:
                  - /url: http://localhost:8000/admin/moderation
                  - img [ref=e289]
                  - generic [ref=e291]: Moderation
          - listitem [ref=e292]:
            - generic [ref=e293] [cursor=pointer]:
              - generic [ref=e294]: Security
              - button "Security" [expanded] [ref=e295]:
                - img [ref=e296]
            - list [ref=e298]:
              - listitem [ref=e299]:
                - link "Audit Logs" [ref=e300] [cursor=pointer]:
                  - /url: http://localhost:8000/admin/audit-log
                  - img [ref=e301]
                  - generic [ref=e303]: Audit Logs
              - listitem [ref=e304]:
                - link "Security Events" [ref=e305] [cursor=pointer]:
                  - /url: http://localhost:8000/admin/security-log
                  - img [ref=e306]
                  - generic [ref=e308]: Security Events
              - listitem [ref=e309]:
                - link "Sessions" [ref=e310] [cursor=pointer]:
                  - /url: http://localhost:8000/admin/sessions
                  - img [ref=e311]
                  - generic [ref=e313]: Sessions
              - listitem [ref=e314]:
                - link "Blocked IPs" [ref=e315] [cursor=pointer]:
                  - /url: http://localhost:8000/admin/blocked-ips
                  - img [ref=e316]
                  - generic [ref=e318]: Blocked IPs
          - listitem [ref=e319]:
            - generic [ref=e320] [cursor=pointer]:
              - generic [ref=e321]: Monitoring
              - button "Monitoring" [expanded] [ref=e322]:
                - img [ref=e323]
            - list [ref=e325]:
              - listitem [ref=e326]:
                - link "Log Viewer" [ref=e327] [cursor=pointer]:
                  - /url: http://localhost:8000/admin/log-viewer
                  - img [ref=e328]
                  - generic [ref=e330]: Log Viewer
              - listitem [ref=e331]:
                - link "Queue Monitor" [ref=e332] [cursor=pointer]:
                  - /url: http://localhost:8000/admin/queue-monitor
                  - img [ref=e333]
                  - generic [ref=e335]: Queue Monitor
              - listitem [ref=e336]:
                - link "Horizon" [ref=e337] [cursor=pointer]:
                  - /url: /horizon
                  - img [ref=e338]
                  - generic [ref=e340]: Horizon
              - listitem [ref=e341]:
                - link "System Health" [ref=e342] [cursor=pointer]:
                  - /url: http://localhost:8000/admin/system-health
                  - img [ref=e343]
                  - generic [ref=e345]: System Health
    - main [ref=e347]:
      - generic [ref=e352]:
        - generic [ref=e353]:
          - generic [ref=e354]:
            - heading "Good evening, Super Admin 👋" [level=1] [ref=e355]
            - paragraph [ref=e356]: Here's what's happening on your platform.
          - generic [ref=e359] [cursor=pointer]:
            - img [ref=e361]
            - generic [ref=e363]: Jul 1, 2026
            - generic [ref=e364]: "|"
            - generic [ref=e365]: Jul 31, 2026
            - img [ref=e367]
        - generic [ref=e369]:
          - generic [ref=e370]:
            - generic [ref=e371]:
              - generic [ref=e372]:
                - generic [ref=e373]: Total Revenue
                - generic [ref=e374]: $2,549.59
              - img [ref=e376]
            - generic [ref=e378]:
              - generic [ref=e379]: ↑ 0%
              - generic [ref=e380]: from last 30 days
          - generic [ref=e381]:
            - generic [ref=e382]:
              - generic [ref=e383]:
                - generic [ref=e384]: Total Orders
                - generic [ref=e385]: "40"
              - img [ref=e387]
            - generic [ref=e390]:
              - generic [ref=e391]: +0 today
              - generic [ref=e392]: 11 paid
          - generic [ref=e393]:
            - generic [ref=e394]:
              - generic [ref=e395]:
                - generic [ref=e396]: Total Students
                - generic [ref=e397]: "103"
              - img [ref=e399]
            - generic [ref=e404]:
              - generic [ref=e405]: +0 today
              - generic [ref=e406]: ↑ 0% enrollments
          - generic [ref=e407]:
            - generic [ref=e408]:
              - generic [ref=e409]:
                - generic [ref=e410]: Total Instructors
                - generic [ref=e411]: "10"
              - img [ref=e413]
            - generic [ref=e417]: All verified
        - generic [ref=e418]:
          - generic [ref=e419]:
            - generic [ref=e420]: Action Required
            - link "View All Alerts ›" [ref=e421] [cursor=pointer]:
              - /url: http://localhost:8000/admin/payments
          - generic [ref=e422]:
            - link "0 Instructor Verifications Pending" [ref=e423] [cursor=pointer]:
              - /url: http://localhost:8000/admin/instructor-verifications
              - img [ref=e425]
              - generic [ref=e428]:
                - generic [ref=e429]: "0"
                - generic [ref=e430]:
                  - text: Instructor Verifications
                  - text: Pending
            - link "0 Refunds This Month" [ref=e431] [cursor=pointer]:
              - /url: http://localhost:8000/admin/refunds
              - img [ref=e433]
              - generic [ref=e436]:
                - generic [ref=e437]: "0"
                - generic [ref=e438]:
                  - text: Refunds
                  - text: This Month
            - link "6 Courses Awaiting Review" [ref=e439] [cursor=pointer]:
              - /url: http://localhost:8000/admin/courses
              - img [ref=e441]
              - generic [ref=e444]:
                - generic [ref=e445]: "6"
                - generic [ref=e446]:
                  - text: Courses
                  - text: Awaiting Review
            - link "0 Failed Payouts Requires Attention" [ref=e447] [cursor=pointer]:
              - /url: http://localhost:8000/admin/payouts
              - img [ref=e449]
              - generic [ref=e451]:
                - generic [ref=e452]: "0"
                - generic [ref=e453]:
                  - text: Failed Payouts
                  - text: Requires Attention
            - link "0 Payment Failures Today" [ref=e454] [cursor=pointer]:
              - /url: http://localhost:8000/admin/payments
              - img [ref=e456]
              - generic [ref=e458]:
                - generic [ref=e459]: "0"
                - generic [ref=e460]:
                  - text: Payment Failures
                  - text: Today
        - generic [ref=e461]:
          - generic [ref=e462]:
            - generic [ref=e463]:
              - generic [ref=e464]:
                - img [ref=e465]
                - text: Revenue Overview
              - generic [ref=e467]:
                - generic [ref=e468]:
                  - generic [ref=e469]: Gross Revenue
                  - generic [ref=e471]: Platform Revenue
                  - generic [ref=e473]: Instructor Revenue
                - generic [ref=e475]:
                  - button "7D" [ref=e476] [cursor=pointer]
                  - button "30D" [ref=e477] [cursor=pointer]
                  - button "6M" [ref=e478] [cursor=pointer]
                  - button "12M" [ref=e479] [cursor=pointer]
            - img [ref=e481]:
              - generic [ref=e482]:
                - generic [ref=e483]: $3K
                - generic [ref=e484]: $2K
                - generic [ref=e485]: $2K
                - generic [ref=e486]: $1K
                - generic [ref=e487]: $510
                - generic [ref=e488]: $0
              - generic [ref=e588]:
                - generic [ref=e589]: Jun 8
                - generic [ref=e590]: Jun 12
                - generic [ref=e591]: Jun 16
                - generic [ref=e592]: Jun 20
                - generic [ref=e593]: Jun 24
                - generic [ref=e594]: Jun 28
                - generic [ref=e595]: Jul 2
                - generic [ref=e596]: Jul 6
                - generic [ref=e597]: Jul 7
          - generic [ref=e598]:
            - generic [ref=e599]:
              - generic [ref=e600]: Gross Revenue
              - generic [ref=e601]: $2,549.59
              - generic [ref=e603]: ↑ 0%
            - generic [ref=e604]:
              - generic [ref=e605]: Platform Revenue
              - generic [ref=e606]: $509.92
              - generic [ref=e608]: ↑ 0%
            - generic [ref=e609]:
              - generic [ref=e610]: Instructor Revenue
              - generic [ref=e611]: $2,039.67
              - generic [ref=e613]: ↑ 0%
        - generic [ref=e614]:
          - generic [ref=e615]:
            - img [ref=e617]
            - generic [ref=e620]:
              - generic [ref=e621]: Published Courses
              - generic [ref=e622]: "6"
              - generic [ref=e624]: +20 this month
          - generic [ref=e625]:
            - img [ref=e627]
            - generic [ref=e630]:
              - generic [ref=e631]: Enrollments (This Month)
              - generic [ref=e632]: "24"
              - generic [ref=e634]: ↑ 0% this month
          - generic [ref=e635]:
            - img [ref=e637]
            - generic [ref=e638]:
              - generic [ref=e639]: Average Completion Rate
              - generic [ref=e640]: 0%
              - generic [ref=e642]: No change
        - generic [ref=e643]:
          - generic [ref=e644]:
            - generic [ref=e645]:
              - img [ref=e646]
              - text: Top Instructors By Revenue
            - link "View All ›" [ref=e648] [cursor=pointer]:
              - /url: http://localhost:8000/admin/instructors
          - table [ref=e649]:
            - rowgroup [ref=e650]:
              - row "# Instructor Students Courses Revenue Growth" [ref=e651]:
                - columnheader "#" [ref=e652]
                - columnheader "Instructor" [ref=e653]
                - columnheader "Students" [ref=e654]
                - columnheader "Courses" [ref=e655]
                - columnheader "Revenue" [ref=e656]
                - columnheader "Growth" [ref=e657]
            - rowgroup [ref=e658]:
              - row "1 DB Dr. Breanne Luettgen dave56@example.org 4 2 $683.18 ↑ 100%" [ref=e659]:
                - cell "1" [ref=e660]:
                  - generic [ref=e661]: "1"
                - cell "DB Dr. Breanne Luettgen dave56@example.org" [ref=e662]:
                  - generic [ref=e663]:
                    - generic [ref=e664]: DB
                    - generic [ref=e665]:
                      - generic [ref=e666]: Dr. Breanne Luettgen
                      - generic [ref=e667]: dave56@example.org
                - cell "4" [ref=e668]
                - cell "2" [ref=e669]
                - cell "$683.18" [ref=e670]
                - cell "↑ 100%" [ref=e671]:
                  - generic [ref=e672]: ↑ 100%
              - row "2 PR Prof. Rowland Jacobson Sr. austen.gorczany@example.org 3 2 $296.21 ↑ 100%" [ref=e673]:
                - cell "2" [ref=e674]:
                  - generic [ref=e675]: "2"
                - cell "PR Prof. Rowland Jacobson Sr. austen.gorczany@example.org" [ref=e676]:
                  - generic [ref=e677]:
                    - generic [ref=e678]: PR
                    - generic [ref=e679]:
                      - generic [ref=e680]: Prof. Rowland Jacobson Sr.
                      - generic [ref=e681]: austen.gorczany@example.org
                - cell "3" [ref=e682]
                - cell "2" [ref=e683]
                - cell "$296.21" [ref=e684]
                - cell "↑ 100%" [ref=e685]:
                  - generic [ref=e686]: ↑ 100%
              - row "3 SM Sidney Mohr klein.cathy@example.net 3 4 $221.31 ↑ 100%" [ref=e687]:
                - cell "3" [ref=e688]:
                  - generic [ref=e689]: "3"
                - cell "SM Sidney Mohr klein.cathy@example.net" [ref=e690]:
                  - generic [ref=e691]:
                    - generic [ref=e692]: SM
                    - generic [ref=e693]:
                      - generic [ref=e694]: Sidney Mohr
                      - generic [ref=e695]: klein.cathy@example.net
                - cell "3" [ref=e696]
                - cell "4" [ref=e697]
                - cell "$221.31" [ref=e698]
                - cell "↑ 100%" [ref=e699]:
                  - generic [ref=e700]: ↑ 100%
              - row "4 MA Marilou Aufderhar DVM ukerluke@example.net 3 1 $198.38 ↑ 100%" [ref=e701]:
                - cell "4" [ref=e702]:
                  - generic [ref=e703]: "4"
                - cell "MA Marilou Aufderhar DVM ukerluke@example.net" [ref=e704]:
                  - generic [ref=e705]:
                    - generic [ref=e706]: MA
                    - generic [ref=e707]:
                      - generic [ref=e708]: Marilou Aufderhar DVM
                      - generic [ref=e709]: ukerluke@example.net
                - cell "3" [ref=e710]
                - cell "1" [ref=e711]
                - cell "$198.38" [ref=e712]
                - cell "↑ 100%" [ref=e713]:
                  - generic [ref=e714]: ↑ 100%
              - row "5 HB Hildegard Blick MD gabriella18@example.org 2 1 $135.76 ↑ 100%" [ref=e715]:
                - cell "5" [ref=e716]:
                  - generic [ref=e717]: "5"
                - cell "HB Hildegard Blick MD gabriella18@example.org" [ref=e718]:
                  - generic [ref=e719]:
                    - generic [ref=e720]: HB
                    - generic [ref=e721]:
                      - generic [ref=e722]: Hildegard Blick MD
                      - generic [ref=e723]: gabriella18@example.org
                - cell "2" [ref=e724]
                - cell "1" [ref=e725]
                - cell "$135.76" [ref=e726]
                - cell "↑ 100%" [ref=e727]:
                  - generic [ref=e728]: ↑ 100%
        - generic [ref=e729]:
          - generic [ref=e730]:
            - generic [ref=e731]:
              - img [ref=e732]
              - text: Most Popular Courses
            - link "View All ›" [ref=e734] [cursor=pointer]:
              - /url: http://localhost:8000/admin/moderation
          - table [ref=e735]:
            - rowgroup [ref=e736]:
              - row "Course Instructor Enrollments Revenue" [ref=e737]:
                - columnheader "Course" [ref=e738]
                - columnheader "Instructor" [ref=e739]
                - columnheader "Enrollments" [ref=e740]
                - columnheader "Revenue" [ref=e741]
            - rowgroup [ref=e742]:
              - row "CO Commodi dolorem qui ut fuga deserunt incidunt. Marketing DB Dr. Breanne Luettgen 2 students $288.12" [ref=e743]:
                - cell "CO Commodi dolorem qui ut fuga deserunt incidunt. Marketing" [ref=e744]:
                  - generic [ref=e745]:
                    - generic [ref=e746]: CO
                    - generic [ref=e747]:
                      - generic [ref=e748]: Commodi dolorem qui ut fuga deserunt incidunt.
                      - generic [ref=e749]: Marketing
                - cell "DB Dr. Breanne Luettgen" [ref=e750]:
                  - generic [ref=e751]:
                    - generic [ref=e752]: DB
                    - generic [ref=e753]: Dr. Breanne Luettgen
                - cell "2 students" [ref=e754]:
                  - generic [ref=e755]:
                    - generic [ref=e756]: "2"
                    - generic [ref=e757]: students
                - cell "$288.12" [ref=e758]
              - row "OP Optio quis natus ratione. Design HB Hildegard Blick MD 2 students $135.76" [ref=e759]:
                - cell "OP Optio quis natus ratione. Design" [ref=e760]:
                  - generic [ref=e761]:
                    - generic [ref=e762]: OP
                    - generic [ref=e763]:
                      - generic [ref=e764]: Optio quis natus ratione.
                      - generic [ref=e765]: Design
                - cell "HB Hildegard Blick MD" [ref=e766]:
                  - generic [ref=e767]:
                    - generic [ref=e768]: HB
                    - generic [ref=e769]: Hildegard Blick MD
                - cell "2 students" [ref=e770]:
                  - generic [ref=e771]:
                    - generic [ref=e772]: "2"
                    - generic [ref=e773]: students
                - cell "$135.76" [ref=e774]
              - row "FU Fugiat assumenda illo qui facilis odio quo. Photography EP Elwin Predovic 1 students $125.96" [ref=e775]:
                - cell "FU Fugiat assumenda illo qui facilis odio quo. Photography" [ref=e776]:
                  - generic [ref=e777]:
                    - generic [ref=e778]: FU
                    - generic [ref=e779]:
                      - generic [ref=e780]: Fugiat assumenda illo qui facilis odio quo.
                      - generic [ref=e781]: Photography
                - cell "EP Elwin Predovic" [ref=e782]:
                  - generic [ref=e783]:
                    - generic [ref=e784]: EP
                    - generic [ref=e785]: Elwin Predovic
                - cell "1 students" [ref=e786]:
                  - generic [ref=e787]:
                    - generic [ref=e788]: "1"
                    - generic [ref=e789]: students
                - cell "$125.96" [ref=e790]
              - row "CO Corrupti ut quia magni recusandae. Health & Wellness MD Miss Delta Raynor 1 students $20.02" [ref=e791]:
                - cell "CO Corrupti ut quia magni recusandae. Health & Wellness" [ref=e792]:
                  - generic [ref=e793]:
                    - generic [ref=e794]: CO
                    - generic [ref=e795]:
                      - generic [ref=e796]: Corrupti ut quia magni recusandae.
                      - generic [ref=e797]: Health & Wellness
                - cell "MD Miss Delta Raynor" [ref=e798]:
                  - generic [ref=e799]:
                    - generic [ref=e800]: MD
                    - generic [ref=e801]: Miss Delta Raynor
                - cell "1 students" [ref=e802]:
                  - generic [ref=e803]:
                    - generic [ref=e804]: "1"
                    - generic [ref=e805]: students
                - cell "$20.02" [ref=e806]
              - row "CU Culpa quia numquam dolores repudiandae ipsa. Health & Wellness HB Hildegard Blick MD 0 students $0.00" [ref=e807]:
                - cell "CU Culpa quia numquam dolores repudiandae ipsa. Health & Wellness" [ref=e808]:
                  - generic [ref=e809]:
                    - generic [ref=e810]: CU
                    - generic [ref=e811]:
                      - generic [ref=e812]: Culpa quia numquam dolores repudiandae ipsa.
                      - generic [ref=e813]: Health & Wellness
                - cell "HB Hildegard Blick MD" [ref=e814]:
                  - generic [ref=e815]:
                    - generic [ref=e816]: HB
                    - generic [ref=e817]: Hildegard Blick MD
                - cell "0 students" [ref=e818]:
                  - generic [ref=e819]:
                    - generic [ref=e820]: "0"
                    - generic [ref=e821]: students
                - cell "$0.00" [ref=e822]
        - generic [ref=e823]:
          - generic [ref=e824]:
            - generic [ref=e825]:
              - generic [ref=e826]:
                - img [ref=e827]
                - text: Recent Orders
              - link "View All ›" [ref=e829] [cursor=pointer]:
                - /url: http://localhost:8000/admin/orders
            - table [ref=e830]:
              - rowgroup [ref=e838]:
                - 'row "Order # Student Course Amount Status Gateway" [ref=e839]':
                  - 'columnheader "Order #" [ref=e840]'
                  - columnheader "Student" [ref=e841]
                  - columnheader "Course" [ref=e842]
                  - columnheader "Amount" [ref=e843]
                  - columnheader "Status" [ref=e844]
                  - columnheader "Gateway" [ref=e845]
              - rowgroup [ref=e846]:
                - row "ORD-0030 Efren Monahan Saepe consequuntur ratione autem. $169.99 Refunded —" [ref=e847]:
                  - cell "ORD-0030" [ref=e848]
                  - cell "Efren Monahan" [ref=e849]
                  - cell "Saepe consequuntur ratione autem." [ref=e850]
                  - cell "$169.99" [ref=e851]
                  - cell "Refunded" [ref=e852]:
                    - generic [ref=e853]: Refunded
                  - cell "—" [ref=e854]
                - row "ORD-0031 Robb Kris Optio quis natus ratione. $124.66 Paid PAYPAL" [ref=e855]:
                  - cell "ORD-0031" [ref=e856]
                  - cell "Robb Kris" [ref=e857]
                  - cell "Optio quis natus ratione." [ref=e858]
                  - cell "$124.66" [ref=e859]
                  - cell "Paid" [ref=e860]:
                    - generic [ref=e861]: Paid
                  - cell "PAYPAL" [ref=e862]
                - row "ORD-0028 Mr. Ismael Watsica Dolores aut autem autem. $261.21 Pending —" [ref=e863]:
                  - cell "ORD-0028" [ref=e864]
                  - cell "Mr. Ismael Watsica" [ref=e865]
                  - cell "Dolores aut autem autem." [ref=e866]
                  - cell "$261.21" [ref=e867]
                  - cell "Pending" [ref=e868]:
                    - generic [ref=e869]: Pending
                  - cell "—" [ref=e870]
                - row "ORD-0029 Gregorio Hill Dolores aut autem autem. $299.16 Failed —" [ref=e871]:
                  - cell "ORD-0029" [ref=e872]
                  - cell "Gregorio Hill" [ref=e873]
                  - cell "Dolores aut autem autem." [ref=e874]
                  - cell "$299.16" [ref=e875]
                  - cell "Failed" [ref=e876]:
                    - generic [ref=e877]: Failed
                  - cell "—" [ref=e878]
                - row "ORD-0032 Hillard Kshlerin Sr. Doloribus omnis non sint iste hic. $264.05 Paid KHQR" [ref=e879]:
                  - cell "ORD-0032" [ref=e880]
                  - cell "Hillard Kshlerin Sr." [ref=e881]
                  - cell "Doloribus omnis non sint iste hic." [ref=e882]
                  - cell "$264.05" [ref=e883]
                  - cell "Paid" [ref=e884]:
                    - generic [ref=e885]: Paid
                  - cell "KHQR" [ref=e886]
          - generic [ref=e887]:
            - generic [ref=e888]:
              - generic [ref=e889]:
                - img [ref=e890]
                - text: Recent Refund Requests
              - link "View All ›" [ref=e893] [cursor=pointer]:
                - /url: http://localhost:8000/admin/refunds
            - table [ref=e894]:
              - rowgroup [ref=e901]:
                - 'row "Refund # Student Amount Reason Status" [ref=e902]':
                  - 'columnheader "Refund #" [ref=e903]'
                  - columnheader "Student" [ref=e904]
                  - columnheader "Amount" [ref=e905]
                  - columnheader "Reason" [ref=e906]
                  - columnheader "Status" [ref=e907]
              - rowgroup [ref=e908]:
                - row "No refunds yet." [ref=e909]:
                  - cell "No refunds yet." [ref=e910]
        - generic [ref=e911]:
          - generic [ref=e912]:
            - generic [ref=e913]:
              - generic [ref=e914]:
                - img [ref=e915]
                - text: Low Rated Courses
                - generic [ref=e917]: < 3 stars
              - link "View All ›" [ref=e918] [cursor=pointer]:
                - /url: http://localhost:8000/admin/reviews
            - table [ref=e919]:
              - rowgroup [ref=e920]:
                - row "Course Instructor Rating Reviews" [ref=e921]:
                  - columnheader "Course" [ref=e922]
                  - columnheader "Instructor" [ref=e923]
                  - columnheader "Rating" [ref=e924]
                  - columnheader "Reviews" [ref=e925]
              - rowgroup [ref=e926]:
                - row "All courses are rated 3 stars or above" [ref=e927]:
                  - cell "All courses are rated 3 stars or above" [ref=e928]:
                    - img [ref=e929]
                    - text: All courses are rated 3 stars or above
          - generic [ref=e931]:
            - generic [ref=e932]:
              - generic [ref=e933]:
                - img [ref=e934]
                - text: System Health
              - generic [ref=e936]: Healthy
            - generic [ref=e938]:
              - generic [ref=e939]:
                - generic [ref=e940]:
                  - img [ref=e941]
                  - text: Queue Jobs Pending
                - generic [ref=e944]: "0"
              - generic [ref=e945]:
                - generic [ref=e946]:
                  - img [ref=e947]
                  - text: Failed Jobs
                - generic [ref=e949]: "0"
              - generic [ref=e950]:
                - generic [ref=e951]:
                  - img [ref=e952]
                  - text: Redis Status
                - generic [ref=e956]: Connected
              - generic [ref=e957]:
                - generic [ref=e958]:
                  - img [ref=e959]
                  - text: Storage Used
                - generic [ref=e963]: 62.9GB / 1006.9GB (6%)
              - generic [ref=e966]:
                - generic [ref=e967]:
                  - img [ref=e968]
                  - text: New Users Today
                - generic [ref=e971]: "0"
              - generic [ref=e972]:
                - generic [ref=e973]:
                  - img [ref=e974]
                  - text: Server Uptime
                - generic [ref=e976]: 0d 3h 32m
  - generic:
    - status
  - generic [ref=e979]:
    - generic [ref=e981]:
      - generic [ref=e982] [cursor=pointer]:
        - generic: Request
      - generic [ref=e983] [cursor=pointer]:
        - generic: Timeline
      - generic [ref=e984] [cursor=pointer]:
        - generic: Views
        - generic [ref=e985]: "1"
      - generic [ref=e986] [cursor=pointer]:
        - generic: Queries
        - generic [ref=e987]: "6"
      - generic [ref=e988] [cursor=pointer]:
        - generic: Models
        - generic [ref=e989]: "3"
      - generic [ref=e990] [cursor=pointer]:
        - generic: Livewire
        - generic [ref=e991]: "2"
      - generic [ref=e992] [cursor=pointer]:
        - generic: Gate
        - generic [ref=e993]: "1"
      - generic [ref=e994] [cursor=pointer]:
        - generic: Cache
        - generic [ref=e995]: "4"
    - generic [ref=e996]:
      - generic [ref=e1003] [cursor=pointer]:
        - generic [ref=e1004]: "7"
        - generic [ref=e1005]: POST /livewire-3319cc50/update
      - generic [ref=e1006] [cursor=pointer]:
        - generic: 66.93ms
      - generic [ref=e1008] [cursor=pointer]:
        - generic: 4MB
      - generic [ref=e1010] [cursor=pointer]:
        - generic: 12.x
```

# Test source

```ts
  140 | // ── tests: cross-group position stability ────────────────────────────────────
  141 | 
  142 | test.describe('Sidebar – cross-group click stays in place', () => {
  143 |     for (const { from, start, target } of CROSS_GROUP_PAIRS) {
  144 |         test(`"${start}" → "${target}" stays in place`, async ({ page }) => {
  145 |             await goto(page, from);
  146 |             // Ensure we're on the "start" page
  147 |             const active = page.locator('.fi-sidebar-item.fi-active').first();
  148 |             const activeText = await active.innerText().catch(() => '');
  149 |             if (!activeText.includes(start)) {
  150 |                 await clickItem(page, start);
  151 |             }
  152 |             await clickItem(page, target);
  153 |         });
  154 |     }
  155 | });
  156 | 
  157 | // ── tests: every single nav item loads without error ─────────────────────────
  158 | 
  159 | test.describe('Sidebar – every nav item navigates successfully', () => {
  160 |     const ALL_ITEMS = [
  161 |         { label: 'Dashboard',             path: '' },
  162 |         // Learning
  163 |         { label: 'Courses',               path: 'pages/courses' },
  164 |         { label: 'Categories',            path: 'resources/categories' },
  165 |         { label: 'Sections',              path: 'resources/sections' },
  166 |         { label: 'Lessons',               path: 'resources/lessons' },
  167 |         { label: 'Instructors',           path: 'resources/instructors' },
  168 |         { label: 'Reviews',               path: 'resources/reviews' },
  169 |         // Commerce
  170 |         { label: 'Orders',                path: 'resources/orders' },
  171 |         { label: 'Payments',              path: 'resources/payments' },
  172 |         { label: 'Coupons',               path: 'resources/coupons' },
  173 |         // People
  174 |         { label: 'Users',                 path: 'resources/users' },
  175 |         { label: 'Verifications',         path: 'resources/instructor-verifications' },
  176 |         // Finance
  177 |         { label: 'Payouts',               path: 'resources/payouts' },
  178 |         { label: 'Wallets',               path: 'resources/wallets' },
  179 |         { label: 'Invoices',              path: 'resources/invoices' },
  180 |         { label: 'Receipts',              path: 'resources/receipts' },
  181 |         // Reports
  182 |         { label: 'User Report',           path: 'reports/users' },
  183 |         { label: 'Revenue Report',        path: 'reports/revenue' },
  184 |         { label: 'Payments Report',       path: 'reports/payments' },
  185 |         { label: 'Payouts Report',        path: 'reports/payouts' },
  186 |         { label: 'Course Intelligence',   path: 'reports/course-intelligence' },
  187 |         { label: 'Instructor Intelligence', path: 'reports/instructor-intelligence' },
  188 |         { label: 'Learning Intelligence', path: 'reports/learning-intelligence' },
  189 |         // System
  190 |         { label: 'Settings',              path: 'settings' },
  191 |         { label: 'Roles',                 path: 'resources/roles' },
  192 |         { label: 'Notifications',         path: 'resources/notifications' },
  193 |         // Security
  194 |         { label: 'Audit Logs',            path: 'audit-logs' },
  195 |         { label: 'Security Events',       path: 'security-events' },
  196 |         { label: 'Sessions',              path: 'sessions' },
  197 |         { label: 'Blocked IPs',           path: 'blocked-ips' },
  198 |         // Monitoring
  199 |         { label: 'Log Viewer',            path: 'log-viewer' },
  200 |         { label: 'Queue Monitor',         path: 'queue-monitor' },
  201 |         { label: 'System Health',         path: 'system-health' },
  202 |     ];
  203 | 
  204 |     test.beforeEach(async ({ page }) => {
  205 |         await goto(page, '');
  206 |     });
  207 | 
  208 |     for (const item of ALL_ITEMS) {
  209 |         test(`clicking "${item.label}" navigates without PHP errors`, async ({ page }) => {
  210 |             // Scroll to make the item visible (groups may need expanding)
  211 |             const btn = page.locator('.fi-sidebar-item-btn, .fi-sidebar-item-button',
  212 |                 { hasText: new RegExp(`^\\s*${item.label}\\s*$`) }).first();
  213 |             await btn.scrollIntoViewIfNeeded();
  214 |             await btn.click();
  215 |             await waitForSidebar(page);
  216 | 
  217 |             // No PHP error page
  218 |             await expect(page.locator('body')).not.toContainText('Whoops!');
  219 |             await expect(page.locator('body')).not.toContainText('500 | Server Error');
  220 | 
  221 |             // Active item is set in sidebar
  222 |             await expect(page.locator('.fi-sidebar-item.fi-active')).toBeVisible();
  223 |         });
  224 |     }
  225 | });
  226 | 
  227 | // ── tests: group collapse / expand ──────────────────────────────────────────
  228 | 
  229 | test.describe('Sidebar – group collapse and expand', () => {
  230 |     test.beforeEach(async ({ page }) => {
  231 |         await goto(page, '');
  232 |     });
  233 | 
  234 |     for (const group of COLLAPSIBLE_GROUPS) {
  235 |         test(`"${group}" group collapses and expands`, async ({ page }) => {
  236 |             const groupEl   = page.locator(`.fi-sidebar-group[data-group-label="${group}"]`);
  237 |             const groupBtn  = groupEl.locator('.fi-sidebar-group-btn').first();
  238 |             const groupItems = groupEl.locator('.fi-sidebar-group-items').first();
  239 | 
> 240 |             await groupBtn.scrollIntoViewIfNeeded();
      |                            ^ Error: locator.scrollIntoViewIfNeeded: Test timeout of 30000ms exceeded.
  241 | 
  242 |             // Ensure the group is EXPANDED before starting the test
  243 |             const isCollapsed = await groupEl.evaluate(el => el.classList.contains('fi-collapsed'));
  244 |             if (isCollapsed) {
  245 |                 await groupBtn.click();
  246 |                 await page.waitForTimeout(300); // x-collapse animation
  247 |             }
  248 |             await expect(groupItems).toBeVisible();
  249 |             await expect(groupEl).not.toHaveClass(/fi-collapsed/);
  250 | 
  251 |             // ── collapse ────────────────────────────────────────────────────
  252 |             await groupBtn.click();
  253 |             await page.waitForTimeout(300);
  254 | 
  255 |             await expect(groupEl).toHaveClass(/fi-collapsed/);
  256 |             await expect(groupItems).not.toBeVisible();
  257 | 
  258 |             // ── expand ──────────────────────────────────────────────────────
  259 |             await groupBtn.click();
  260 |             await page.waitForTimeout(300);
  261 | 
  262 |             await expect(groupEl).not.toHaveClass(/fi-collapsed/);
  263 |             await expect(groupItems).toBeVisible();
  264 |         });
  265 |     }
  266 | });
  267 | 
```