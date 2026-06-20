# MaskZero Site Audit

target: https://maskzero.site
generated: 2026-06-20T01:39:44.160Z
verdict: FAIL

## Failures
- /spark/ returned bad status: 404
- /spark/ has browser console/page errors
- Theme/nav missing on: /, /spark-signup/, /spark-login/, /spark-generator/, /spark-dashboard/, /quiz/, /missions/, /spark/

## Pages
| Route | Status | H1 | Shared Nav | Theme CSS | Legacy Branding | Errors |
|---|---:|---|---:|---:|---:|---:|
| / | 200 | MASKZERO | no | no | no | 0 |
| /spark-signup/ | 200 | CREATE YOUR SPARK ACCOUNT | no | no | no | 0 |
| /spark-login/ | 200 | LOG IN | no | no | no | 0 |
| /spark-generator/ | 200 | MASKZERO QUIZ | no | no | no | 0 |
| /spark-dashboard/ | 200 | COMMAND POST | no | no | no | 0 |
| /quiz/ | 200 | MASKZERO QUIZ | no | no | no | 0 |
| /missions/ | 200 | ANSWER THE DISPATCH. | no | no | no | 0 |
| /spark/ | 404 | This Page Does Not Exist | no | no | no | 2 |

## Internal Link Checks
| From | Text | Status | Href |
|---|---|---:|---|
| / | MASKZERO | 200 | https://maskzero.site/ |
| / | CREATE HERO | 200 | https://maskzero.site/create-hero/ |
| / | HOW IT WORKS | 200 | https://maskzero.site/how-it-works/ |
| / | MERIDIAN CITY | 200 | https://maskzero.site/meridian-map/ |
| / | DISPATCH | 200 | https://maskzero.site/dispatch/ |
| / | LOGIN | 200 | https://maskzero.site/login/ |
| / | CREATE YOUR SPARK | 200 | https://maskzero.site/create-hero/ |
| / | ANSWER THE DISPATCH | 200 | https://maskzero.site/dispatch/ |
| /spark-signup/ | Cover | 200 | https://maskzero.site/ |
| /spark-signup/ | Origin Lab | 200 | https://maskzero.site/create-hero/ |
| /spark-signup/ | Log In | 200 | https://maskzero.site/login/ |
| /spark-signup/ | Join The Universe | 200 | https://maskzero.site/spark-signup/ |
| /spark-signup/ | I ALREADY HAVE AN ACCOUNT | 200 | https://maskzero.site/spark-login/ |
| /spark-signup/ | Home | 200 | https://maskzero.site/ |
| /spark-signup/ | How It Works | 200 | https://maskzero.site/#how-it-works |
| /spark-signup/ | Log In | 200 | https://maskzero.site/spark-login/ |
| /spark-login/ | Cover | 200 | https://maskzero.site/ |
| /spark-login/ | Origin Lab | 200 | https://maskzero.site/create-hero/ |
| /spark-login/ | Log In | 200 | https://maskzero.site/login/ |
| /spark-login/ | Join The Universe | 200 | https://maskzero.site/spark-signup/ |
| /spark-login/ | CREATE ACCOUNT | 200 | https://maskzero.site/spark-signup/ |
| /spark-login/ | Forgot password? | 200 | https://maskzero.site/spark-login/?help=lost-password |
| /spark-login/ | Home | 200 | https://maskzero.site/ |
| /spark-login/ | Create Account | 200 | https://maskzero.site/spark-signup/ |
| /spark-generator/ | HOME | 200 | https://maskzero.site/ |
| /spark-generator/ | QUIZ | 200 | https://maskzero.site/quiz/ |
| /spark-generator/ | EMERGENCE | 200 | https://maskzero.site/the-emergence/ |
| /spark-generator/ | MISSIONS | 200 | https://maskzero.site/missions/ |
| /spark-generator/ | BATTLES | 200 | https://maskzero.site/battles/ |
| /spark-dashboard/ | Cover | 200 | https://maskzero.site/ |
| /spark-dashboard/ | Origin Lab | 200 | https://maskzero.site/create-hero/ |
| /spark-dashboard/ | Log In | 200 | https://maskzero.site/login/ |
| /spark-dashboard/ | Join The Universe | 200 | https://maskzero.site/spark-signup/ |
| /spark-dashboard/ | CREATE HERO | 200 | https://maskzero.site/spark-signup/ |
| /spark-dashboard/ | LOG IN | 200 | https://maskzero.site/spark-login/?redirect_to=%2Fspark-dashboard%2F |
| /spark-dashboard/ | LOG IN TO START QUIZ | 200 | https://maskzero.site/spark-login/?redirect_to=%2Fspark-generator%2F |
| /spark-dashboard/ | LOG IN TO UNLOCK DISPATCH | 200 | https://maskzero.site/spark-login/?redirect_to=%2Fmeridian-dispatch%2F |
| /spark-dashboard/ | START QUIZ | 200 | https://maskzero.site/spark-login/?redirect_to=%2Fspark-generator%2F |
| /spark-dashboard/ | OPEN DISPATCH | 200 | https://maskzero.site/spark-login/?redirect_to=%2Fmeridian-dispatch%2F |
| /spark-dashboard/ | OPEN MAP | 200 | https://maskzero.site/spark-login/?redirect_to=%2Fmeridian-map%2F |
| /spark-dashboard/ | cover | 200 | https://maskzero.site/ |
| /spark-dashboard/ | create one official hero | 200 | https://maskzero.site/spark-signup/ |
| /spark-dashboard/ | Open | 200 | https://maskzero.site/spark-generator/ |
| /spark-dashboard/ | Open | 200 | https://maskzero.site/meridian-dispatch/ |
| /spark-dashboard/ | Open | 200 | https://maskzero.site/spark-battle/ |
| /spark-dashboard/ | Inbox
        MaskZero field reports | 200 | https://maskzero.site/spark-inbox/ |
| /spark-dashboard/ | Dispatch
        Headlines become missions | 200 | https://maskzero.site/meridian-dispatch/ |
| /spark-dashboard/ | Map
        Sectors A1–P15 | 200 | https://maskzero.site/meridian-map/ |
| /spark-dashboard/ | News
        City wire & advisories | 200 | https://maskzero.site/news/ |
| /spark-dashboard/ | Generator
        Origin quiz & hero lock | 200 | https://maskzero.site/spark-generator/ |
| /spark-dashboard/ | Battle Sim
        v8.6 duel resolver | 200 | https://maskzero.site/spark-battle/ |
| /spark-dashboard/ | Gauntlet
        Meridian trials | 200 | https://maskzero.site/spark-gauntlet/ |
| /spark-dashboard/ | Open Inbox | 200 | https://maskzero.site/spark-inbox/ |
| /spark-dashboard/ | Respond To Mission | 200 | https://maskzero.site/meridian-dispatch/ |
| /spark-dashboard/ | Generate Spark | 200 | https://maskzero.site/spark-generator/ |
| /spark-dashboard/ | Account Rules | 200 | https://maskzero.site/spark-account/ |
| /spark-dashboard/ | View Cover | 200 | https://maskzero.site/ |
| /spark-dashboard/ | Origin Rules | 200 | https://maskzero.site/spark-dashboard/#origin-rules |
| /spark-dashboard/ | Log Out | 200 | https://maskzero.site/spark-logout/ |
| /quiz/ | HOME | 200 | https://maskzero.site/ |
| /quiz/ | QUIZ | 200 | https://maskzero.site/quiz/ |
| /quiz/ | EMERGENCE | 200 | https://maskzero.site/the-emergence/ |
| /quiz/ | MISSIONS | 200 | https://maskzero.site/missions/ |
| /quiz/ | BATTLES | 200 | https://maskzero.site/battles/ |
| /missions/ | HOME | 200 | https://maskzero.site/ |
| /missions/ | HOW IT WORKS | 200 | https://maskzero.site/how-it-works/ |
| /missions/ | GENERATE SPARK | 200 | https://maskzero.site/spark-generator/ |
| /missions/ | MISSIONS | 200 | https://maskzero.site/missions/ |
| /missions/ | BATTLES | 200 | https://maskzero.site/battles/ |
| /missions/ | GENERATE OR SAVE A SPARK | 200 | https://maskzero.site/spark-generator/ |
| /missions/ | OPEN WHAT-IF ARENA | 200 | https://maskzero.site/battles/ |
| /missions/ | GENERATE YOUR SPARK | 200 | https://maskzero.site/spark-generator/ |

## Forms / Buttons
```json
[
  {
    "route": "/spark-signup/",
    "inputs": [
      {
        "name": "email",
        "type": "email",
        "required": true
      },
      {
        "name": "username",
        "type": "text",
        "required": false
      },
      {
        "name": "password",
        "type": "password",
        "required": true
      }
    ],
    "buttons": [
      {
        "text": "MENU",
        "type": "button"
      },
      {
        "text": "Close Menu",
        "type": "button"
      },
      {
        "text": "SHOW",
        "type": "button"
      },
      {
        "text": "CREATE ACCOUNT",
        "type": "submit"
      }
    ]
  },
  {
    "route": "/spark-login/",
    "inputs": [
      {
        "name": "log",
        "type": "text",
        "required": true
      },
      {
        "name": "pwd",
        "type": "password",
        "required": true
      },
      {
        "name": "redirect_to",
        "type": "hidden",
        "required": false
      },
      {
        "name": "rememberme",
        "type": "hidden",
        "required": false
      }
    ],
    "buttons": [
      {
        "text": "MENU",
        "type": "button"
      },
      {
        "text": "Close Menu",
        "type": "button"
      },
      {
        "text": "SHOW",
        "type": "button"
      },
      {
        "text": "LOG IN",
        "type": "submit"
      }
    ]
  },
  {
    "route": "/spark-generator/",
    "inputs": [],
    "buttons": [
      {
        "text": "START SPARK QUIZ",
        "type": "button"
      },
      {
        "text": "RESET",
        "type": "button"
      },
      {
        "text": "Continue To Flavor Pass",
        "type": "button"
      }
    ]
  },
  {
    "route": "/spark-dashboard/",
    "inputs": [],
    "buttons": [
      {
        "text": "MENU",
        "type": "button"
      },
      {
        "text": "Close Menu",
        "type": "button"
      }
    ]
  },
  {
    "route": "/quiz/",
    "inputs": [],
    "buttons": [
      {
        "text": "START SPARK QUIZ",
        "type": "button"
      },
      {
        "text": "RESET",
        "type": "button"
      }
    ]
  },
  {
    "route": "/missions/",
    "inputs": [],
    "buttons": [
      {
        "text": "SELECT MISSION",
        "type": "button"
      },
      {
        "text": "SELECT MISSION",
        "type": "button"
      },
      {
        "text": "SELECT MISSION",
        "type": "button"
      }
    ]
  }
]
```