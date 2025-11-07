# Page snapshot

```yaml
- generic [ref=e2]:
  - link [ref=e4] [cursor=pointer]:
    - /url: /
    - img [ref=e5]
  - generic [ref=e9]:
    - generic [ref=e10]:
      - generic [ref=e11]: Email
      - textbox "Email" [ref=e12]: userstaff@motac.gov.my
    - generic [ref=e13]:
      - generic [ref=e14]: Password
      - textbox "Password" [ref=e15]: password
    - generic [ref=e17]:
      - checkbox "Remember me" [disabled] [ref=e18]
      - generic [ref=e19]: Remember me
    - generic [ref=e20]:
      - link "Forgot your password?" [ref=e21] [cursor=pointer]:
        - /url: http://localhost:8000/forgot-password
      - button "Log in" [disabled] [ref=e22]
```