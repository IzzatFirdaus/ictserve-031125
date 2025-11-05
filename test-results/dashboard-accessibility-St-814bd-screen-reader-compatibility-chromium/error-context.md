# Page snapshot

```yaml
- generic [ref=e2]:
  - link [ref=e4] [cursor=pointer]:
    - /url: /
    - img [ref=e5]
  - generic [ref=e9]:
    - generic [ref=e10]:
      - generic [ref=e11]: Email
      - textbox "Email" [ref=e12]: test@motac.gov.my
      - list [ref=e13]:
        - listitem [ref=e14]: These credentials do not match our records.
    - generic [ref=e15]:
      - generic [ref=e16]: Password
      - textbox "Password" [ref=e17]: password
    - generic [ref=e19]:
      - checkbox "Remember me" [ref=e20]
      - generic [ref=e21]: Remember me
    - generic [ref=e22]:
      - link "Forgot your password?" [ref=e23] [cursor=pointer]:
        - /url: http://localhost:8000/forgot-password
      - button "Log in" [ref=e24] [cursor=pointer]
```