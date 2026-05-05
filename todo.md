# TODO

# 05-04-2026

# add

      -listOfSupervisory
      -field username
      -seeder offices

METHOD:GET
http://192.168.8.182:9000/api/employee/list-of-supervisory

sample data
{
"success": true,
"message": "Fetch employee successfully",
"data": [
{
"name": "JOGRAD M. MAHUSAY",
"position": "INFORMATION SYSTEMS ANALYST III",
"ControlNo": "011790",
"office": "OFFICE OF THE CITY INFORMATION AND COMMUNICATIONS TECHNOLOGY MANAGEMENT OFFICER",
"status": "REGULAR"
}
]
}

    METHOD:POST
    http://192.168.8.182:9000/api/user/supervisory

    sample data required
    {
        "name": [
                "The name field is required."
            ],
            "designation": [
                "The designation field is required."
            ],
            "role_id": [
                "The role id field is required."
            ],
            "controlNo": [
                "The control no field is required."
            ],
            "username": [
                "The username field is required."
            ],
            "password": [
                "The password field is required."
            ]

    }

# done

-list of supervisory
-create account of supervisory

# 06-05-2026

# add

     -rated_by on qpef
     -role supervisory
     - change supervisory list into  employee head
     - active or inactive

            http://192.168.8.182:9000/api/user/supervisor-role

            {
            "success": true,
            "message": "Fetch Successfully",
            "data": [
                {
                    "role_id": "4",
                    "name": "supervisor_admin"
                }
            ]
        }


        http://192.168.8.182:9000/api/employee/list-of-Head
        {
    "success": true,
    "message": "Fetch employee successful",
    "data": [
        {
            "name": "JOSEPH NELSON N. BRIONES",
            "position": "CITY GOVERNMENT DEPARTMENT HEAD I",
            "ControlNo": "003041",
            "office": "OFFICE OF THE CITY INFORMATION AND COMMUNICATIONS TECHNOLOGY MANAGEMENT OFFICER",
            "job_title": "Office Head",
            "status": "REGULAR"
        }
    ]

}

        Method:GET
        list of head account
        http://192.168.8.182:9000/api/user/head-account
            {
        "success": true,
        "message": "Fetch Successfully",
        "data": [
            {
                "id": 21,
                "name": "JOGRAD M. MAHUSAY",
                "email": null,
                "email_verified_at": null,
                "designation": "INFORMATION SYSTEMS ANALYST III",
                "role_id": 4,
                "office_id": 15,
                "created_at": "2026-05-04T01:09:28.417000Z",
                "updated_at": "2026-05-04T02:08:15.503000Z",
                "control_no": "011790",
                "username": "Mahusay",
                "active": "1"
            }
        ]
    }

        Method:POST
        reset password
        http://192.168.8.182:9000/api/user/reset-password/{userId}

        Method:DELETE
        delete user account
        http://192.168.8.182:9000/api/user/delete/{userId}

        Method:POST
        http://192.168.8.182:9000/api/user/update/head-account

             {
            "success": false,
            "message": "Validation failed",
            "errors": {
                "userId": [
                    "The user id field is required."
                ],
                "active": [
                    "The active field is required."
                ]
            }

        }


        Method:GET
        view detail account of head 
        http://192.168.8.182:9000/api/user/view/account/21
        {
    "message": "User retrieved successfully.",
    "data": {
        "id": 21,
        "name": "JOGRAD M. MAHUSAY",
        "email": null,
        "email_verified_at": null,
        "designation": "INFORMATION SYSTEMS ANALYST III",
        "role_id": 4, 
        "office_id": 15,
        "created_at": "2026-05-04T01:09:28.417000Z",
        "updated_at": "2026-05-04T02:08:15.503000Z",
        "control_no": "011790",
        "username": "Mahusay",
        "active": "1",
        "office": {
            "id": 15,
            "name": "OFFICE OF THE CITY INFORMATION AND COMMUNICATIONS TECHNOLOGY MANAGEMENT OFFICER",
            "created_at": "2026-05-03T23:23:41.063000Z",
            "updated_at": "2026-05-03T23:23:41.063000Z"
        },
        "role": {
            "id": 4,
            "name": "supervisor_admin",
            "created_at": "2026-04-20T05:50:45.383000Z",
            "updated_at": "2026-04-20T05:50:45.383000Z",
            "label": null
        }
    }

    Method:GET  
    get the employee of Head
    http://192.168.8.182:9000/api/employee/head
  [
    {
        "controlNo": "022395",
        "name": "DENIEL S. TOMENIO",
        "status": "CONTRACTUAL",
        "position": "SECURITY AIDE (JOB ORDER)"
    },
    {
        "controlNo": "011789",
        "name": "NEIL BENJAMIN P. ROBLE",
        "status": "CASUAL",
        "position": "ADMINISTRATIVE AIDE III (CLERK I) (CASUAL)"
    },
    {
        "controlNo": "021786",
        "name": "ALEXANDRA P. ANIÑON",
        "status": "CONTRACTUAL",
        "position": "ADMINISTRATIVE ASSISTANT (JOB ORDER)"
    }
]
}
