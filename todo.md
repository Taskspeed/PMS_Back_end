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


METHOD:POST
//get multiple qpef
http://192.168.8.182:9000/api/qpef/employee/quarter

error
{
    "controlNo": ["022395", "022485", "021736"],
    "quarter": "Q1",
    "year": "2026"
}


succeess
[
    {
        "id": 9,
        "control_no": "022395",
        "quarterly": "Q1",
        "year": "2026",
        "rated_by": null,
        "job_performance": {
            "items": [
                {
                    "id": 39,
                    "qpef_id": "9",
                    "indicators": "Task",
                    "rating": "4",
                    "remarks": "Improved performance",
                    "created_at": "2026-02-15T18:47:23.987000Z",
                    "updated_at": "2026-02-15T18:51:16.237000Z"
                },
                {
                    "id": 40,
                    "qpef_id": "9",
                    "indicators": "Productivity",
                    "rating": "5",
                    "remarks": "Excellent output",
                    "created_at": "2026-02-15T18:51:16.240000Z",
                    "updated_at": "2026-02-15T18:51:16.240000Z"
                }
            ],
            "sub_total": 4.5,
            "weight": "40%",
            "weighted_score": 1.8
        },
        "competencies_attitude": {
            "items": [
                {
                    "id": 26,
                    "qpef_id": "9",
                    "indicators": "Professionalism",
                    "rating": "5",
                    "remarks": "Very professional",
                    "created_at": "2026-02-15T18:47:23.990000Z",
                    "updated_at": "2026-02-15T18:51:16.240000Z"
                }
            ],
            "sub_total": 5,
            "weight": "50%",
            "weighted_score": 2.5
        },
        "physical_mental": {
            "items": [
                {
                    "id": 20,
                    "qpef_id": "9",
                    "indicators": "Attendancetest",
                    "rating": "5",
                    "remarks": "Perfect attendance",
                    "created_at": "2026-02-15T18:47:23.993000Z",
                    "updated_at": "2026-02-15T18:51:16.243000Z"
                }
            ],
            "sub_total": 5,
            "weight": "10%",
            "weighted_score": 0.5
        },
        "recommendation_development": {
            "id": 9,
            "qpef_id": "9",
            "for_retention": "1",
            "for_commendation": "1",
            "for_improvement": "1",
            "for_non_renewal": "1",
            "recommendation": "Recommended for retention.",
            "created_at": "2026-02-15T18:47:24.000000Z",
            "updated_at": "2026-02-15T18:51:16.247000Z"
        },
        "final_rating": {
            "job_performance_weighted_score": 1.8,
            "competencies_attitude_weighted_score": 2.5,
            "physical_mental_weighted_score": 0.5,
            "final_rating": 4.8
        }
    },
    {
        "id": 10,
        "control_no": "022485",
        "quarterly": "Q1",
        "year": "2026",
        "rated_by": null,
        "job_performance": {
            "items": [
                {
                    "id": 41,
                    "qpef_id": "10",
                    "indicators": "Accomplishes assigned tasks efficiently and on time",
                    "rating": "5",
                    "remarks": "5",
                    "created_at": "2026-02-15T20:53:30.090000Z",
                    "updated_at": "2026-02-15T21:23:42.587000Z"
                },
                {
                    "id": 42,
                    "qpef_id": "10",
                    "indicators": "Demonstrates quality and accuracy in work output",
                    "rating": "5",
                    "remarks": "5",
                    "created_at": "2026-02-15T20:53:30.090000Z",
                    "updated_at": "2026-02-15T21:23:42.587000Z"
                },
                {
                    "id": 43,
                    "qpef_id": "10",
                    "indicators": "Observes proper work processes and procedures",
                    "rating": "5",
                    "remarks": "5",
                    "created_at": "2026-02-15T20:53:30.093000Z",
                    "updated_at": "2026-02-15T21:23:42.590000Z"
                },
                {
                    "id": 44,
                    "qpef_id": "10",
                    "indicators": "Shows initiative and resourcefulness in completing tasks",
                    "rating": "5",
                    "remarks": "5",
                    "created_at": "2026-02-15T20:53:30.097000Z",
                    "updated_at": "2026-02-15T21:23:42.590000Z"
                }
            ],
            "sub_total": 5,
            "weight": "40%",
            "weighted_score": 2
        },
        "competencies_attitude": {
            "items": [
                {
                    "id": 27,
                    "qpef_id": "10",
                    "indicators": "Demonstrates cooperation and teamwork",
                    "rating": "5",
                    "remarks": "5",
                    "created_at": "2026-02-15T20:53:30.097000Z",
                    "updated_at": "2026-02-15T21:23:42.593000Z"
                },
                {
                    "id": 28,
                    "qpef_id": "10",
                    "indicators": "Exhibits professionalism, courtesy, and respect in dealing with co-workers and clients",
                    "rating": "5",
                    "remarks": "5",
                    "created_at": "2026-02-15T20:53:30.100000Z",
                    "updated_at": "2026-02-15T21:23:42.593000Z"
                },
                {
                    "id": 29,
                    "qpef_id": "10",
                    "indicators": "Demonstrates reliability, honesty, and integrity",
                    "rating": "5",
                    "remarks": "5",
                    "created_at": "2026-02-15T20:53:30.103000Z",
                    "updated_at": "2026-02-15T21:23:42.593000Z"
                },
                {
                    "id": 30,
                    "qpef_id": "10",
                    "indicators": "Adapts well to changing work assignments and challenges",
                    "rating": "5",
                    "remarks": "5",
                    "created_at": "2026-02-15T20:53:30.107000Z",
                    "updated_at": "2026-02-15T21:23:42.597000Z"
                },
                {
                    "id": 31,
                    "qpef_id": "10",
                    "indicators": "Reports accurate information and spot errors in documents and other forms of communication",
                    "rating": "5",
                    "remarks": "5",
                    "created_at": "2026-02-15T20:53:30.107000Z",
                    "updated_at": "2026-02-15T21:23:42.597000Z"
                },
                {
                    "id": 32,
                    "qpef_id": "10",
                    "indicators": "Adheres to agency's internal policies, office rules and regulations",
                    "rating": "5",
                    "remarks": "5",
                    "created_at": "2026-02-15T20:53:30.110000Z",
                    "updated_at": "2026-02-15T21:23:42.597000Z"
                },
                {
                    "id": 33,
                    "qpef_id": "10",
                    "indicators": "Apply and adapt record management standards which maintains and organized records",
                    "rating": "5",
                    "remarks": "5",
                    "created_at": "2026-02-15T20:53:30.110000Z",
                    "updated_at": "2026-02-15T21:23:42.600000Z"
                },
                {
                    "id": 34,
                    "qpef_id": "10",
                    "indicators": "Demonstrates attention to detail on documents, task and procedures",
                    "rating": "5",
                    "remarks": "5",
                    "created_at": "2026-02-15T20:53:30.113000Z",
                    "updated_at": "2026-02-15T21:23:42.600000Z"
                }
            ],
            "sub_total": 5,
            "weight": "50%",
            "weighted_score": 2.5
        },
        "physical_mental": {
            "items": [
                {
                    "id": 21,
                    "qpef_id": "10",
                    "indicators": "Maintains focus, alertness and manages work-related stress effectively",
                    "rating": "5",
                    "remarks": "5",
                    "created_at": "2026-02-15T20:53:30.117000Z",
                    "updated_at": "2026-02-15T21:23:42.600000Z"
                },
                {
                    "id": 22,
                    "qpef_id": "10",
                    "indicators": "Demonstrates physical ability to perform assigned tasks",
                    "rating": "5",
                    "remarks": "55",
                    "created_at": "2026-02-15T20:53:30.117000Z",
                    "updated_at": "2026-02-15T21:23:42.603000Z"
                },
                {
                    "id": 23,
                    "qpef_id": "10",
                    "indicators": "Observes proper grooming and personal hygiene",
                    "rating": "5",
                    "remarks": "5",
                    "created_at": "2026-02-15T20:53:30.120000Z",
                    "updated_at": "2026-02-15T21:23:42.603000Z"
                }
            ],
            "sub_total": 5,
            "weight": "10%",
            "weighted_score": 0.5
        },
        "recommendation_development": {
            "id": 10,
            "qpef_id": "10",
            "for_retention": "1",
            "for_commendation": "0",
            "for_improvement": "0",
            "for_non_renewal": "0",
            "recommendation": "fdsgsdfgs",
            "created_at": "2026-02-15T20:53:30.123000Z",
            "updated_at": "2026-02-15T21:23:42.607000Z"
        },
        "final_rating": {
            "job_performance_weighted_score": 2,
            "competencies_attitude_weighted_score": 2.5,
            "physical_mental_weighted_score": 0.5,
            "final_rating": 5
        }
    },
    {
        "id": 12,
        "control_no": "021786",
        "quarterly": "Q1",
        "year": "2026",
        "rated_by": null,
        "job_performance": {
            "items": [
                {
                    "id": 49,
                    "qpef_id": "12",
                    "indicators": "Accomplishes assigned tasks efficiently and on time",
                    "rating": "1",
                    "remarks": null,
                    "created_at": "2026-05-05T00:38:18.777000Z",
                    "updated_at": "2026-05-05T00:38:18.777000Z"
                },
                {
                    "id": 50,
                    "qpef_id": "12",
                    "indicators": "Demonstrates quality and accuracy in work output",
                    "rating": "1",
                    "remarks": null,
                    "created_at": "2026-05-05T00:38:18.780000Z",
                    "updated_at": "2026-05-05T00:38:18.780000Z"
                },
                {
                    "id": 51,
                    "qpef_id": "12",
                    "indicators": "Observes proper work processes and procedures",
                    "rating": "1",
                    "remarks": null,
                    "created_at": "2026-05-05T00:38:18.783000Z",
                    "updated_at": "2026-05-05T00:38:18.783000Z"
                },
                {
                    "id": 52,
                    "qpef_id": "12",
                    "indicators": "Shows initiative and resourcefulness in completing tasks",
                    "rating": "1",
                    "remarks": null,
                    "created_at": "2026-05-05T00:38:18.787000Z",
                    "updated_at": "2026-05-05T00:38:18.787000Z"
                }
            ],
            "sub_total": 1,
            "weight": "40%",
            "weighted_score": 0.4
        },
        "competencies_attitude": {
            "items": [
                {
                    "id": 43,
                    "qpef_id": "12",
                    "indicators": "Demonstrates cooperation and teamwork",
                    "rating": "1",
                    "remarks": null,
                    "created_at": "2026-05-05T00:38:18.790000Z",
                    "updated_at": "2026-05-05T00:38:18.790000Z"
                },
                {
                    "id": 44,
                    "qpef_id": "12",
                    "indicators": "Exhibits professionalism, courtesy, and respect in dealing with co-workers and clients",
                    "rating": "1",
                    "remarks": null,
                    "created_at": "2026-05-05T00:38:18.793000Z",
                    "updated_at": "2026-05-05T00:38:18.793000Z"
                },
                {
                    "id": 45,
                    "qpef_id": "12",
                    "indicators": "Demonstrates reliability, honesty, and integrity",
                    "rating": "1",
                    "remarks": null,
                    "created_at": "2026-05-05T00:38:18.797000Z",
                    "updated_at": "2026-05-05T00:38:18.797000Z"
                },
                {
                    "id": 46,
                    "qpef_id": "12",
                    "indicators": "Adapts well to changing work assignments and challenges",
                    "rating": "1",
                    "remarks": null,
                    "created_at": "2026-05-05T00:38:18.797000Z",
                    "updated_at": "2026-05-05T00:38:18.797000Z"
                },
                {
                    "id": 47,
                    "qpef_id": "12",
                    "indicators": "Reports accurate information and spot errors in documents and other forms of communication",
                    "rating": "1",
                    "remarks": null,
                    "created_at": "2026-05-05T00:38:18.800000Z",
                    "updated_at": "2026-05-05T00:38:18.800000Z"
                },
                {
                    "id": 48,
                    "qpef_id": "12",
                    "indicators": "Adheres to agency's internal policies, office rules and regulations",
                    "rating": "1",
                    "remarks": null,
                    "created_at": "2026-05-05T00:38:18.803000Z",
                    "updated_at": "2026-05-05T00:38:18.803000Z"
                },
                {
                    "id": 49,
                    "qpef_id": "12",
                    "indicators": "Apply and adapt record management standards which maintains and organized records",
                    "rating": "1",
                    "remarks": null,
                    "created_at": "2026-05-05T00:38:18.807000Z",
                    "updated_at": "2026-05-05T00:38:18.807000Z"
                },
                {
                    "id": 50,
                    "qpef_id": "12",
                    "indicators": "Demonstrates attention to detail on documents, task and procedures",
                    "rating": "1",
                    "remarks": null,
                    "created_at": "2026-05-05T00:38:18.807000Z",
                    "updated_at": "2026-05-05T00:38:18.807000Z"
                }
            ],
            "sub_total": 1,
            "weight": "50%",
            "weighted_score": 0.5
        },
        "physical_mental": {
            "items": [
                {
                    "id": 27,
                    "qpef_id": "12",
                    "indicators": "Maintains focus, alertness and manages work-related stress effectively",
                    "rating": "0",
                    "remarks": null,
                    "created_at": "2026-05-05T00:38:18.810000Z",
                    "updated_at": "2026-05-05T00:38:18.810000Z"
                },
                {
                    "id": 28,
                    "qpef_id": "12",
                    "indicators": "Demonstrates physical ability to perform assigned tasks",
                    "rating": "0",
                    "remarks": null,
                    "created_at": "2026-05-05T00:38:18.817000Z",
                    "updated_at": "2026-05-05T00:38:18.817000Z"
                },
                {
                    "id": 29,
                    "qpef_id": "12",
                    "indicators": "Observes proper grooming and personal hygiene",
                    "rating": "0",
                    "remarks": null,
                    "created_at": "2026-05-05T00:38:18.817000Z",
                    "updated_at": "2026-05-05T00:38:18.817000Z"
                }
            ],
            "sub_total": 0,
            "weight": "10%",
            "weighted_score": 0
        },
        "recommendation_development": {
            "id": 12,
            "qpef_id": "12",
            "for_retention": "0",
            "for_commendation": "0",
            "for_improvement": "0",
            "for_non_renewal": "0",
            "recommendation": null,
            "created_at": "2026-05-05T00:38:18.823000Z",
            "updated_at": "2026-05-05T00:38:18.823000Z"
        },
        "final_rating": {
            "job_performance_weighted_score": 0.4,
            "competencies_attitude_weighted_score": 0.5,
            "physical_mental_weighted_score": 0,
            "final_rating": 0.9
        }
    }


    // updating  employee head send parameter year 
    
    http://192.168.8.182:9000/api/employee/head?year=2026

    {
    "employee": [
        {
            "controlNo": "022395",
            "name": "DENIEL S. TOMENIO",
            "status": "CONTRACTUAL",
            "position": "SECURITY AIDE (JOB ORDER)",
            "job_title": "Employee",
            "office": "OFFICE OF THE CITY INFORMATION AND COMMUNICATIONS TECHNOLOGY MANAGEMENT OFFICER",
            "qpef": [
                {
                    "id": 9,
                    "control_no": "022395",
                    "year": "2026",
                    "quarterly": "Q1",
                    "rated_by": null
                },
                {
                    "id": 13,
                    "control_no": "022395",
                    "year": "2026",
                    "quarterly": "Q2",
                    "rated_by": "deniel"
                }
            ]
        },
        {
            "controlNo": "011789",
            "name": "NEIL BENJAMIN P. ROBLE",
            "status": "CASUAL",
            "position": "ADMINISTRATIVE AIDE III (CLERK I) (CASUAL)",
            "job_title": "Employee",
            "office": "OFFICE OF THE CITY INFORMATION AND COMMUNICATIONS TECHNOLOGY MANAGEMENT OFFICER",
            "qpef": []
        },
        {
            "controlNo": "021786",
            "name": "ALEXANDRA P. ANIÑON",
            "status": "CONTRACTUAL",
            "position": "ADMINISTRATIVE ASSISTANT (JOB ORDER)",
            "job_title": "Employee",
            "office": "OFFICE OF THE CITY INFORMATION AND COMMUNICATIONS TECHNOLOGY MANAGEMENT OFFICER",
            "qpef": [
                {
                    "id": 12,
                    "control_no": "021786",
                    "year": "2026",
                    "quarterly": "Q1",
                    "rated_by": null
                }
            ]
        }
    ],
    "immediate_supervisor": {
        "name": "JOGRAD M. MAHUSAY",
        "position": "INFORMATION SYSTEMS ANALYST III"
    },
    "department_office": {
        "id": 87,
        "name": "JOSEPH NELSON N. BRIONES",
        "rank": "Employee",
        "ControlNo": "003041",
        "position": "CITY GOVERNMENT DEPARTMENT HEAD I",
        "office": "OFFICE OF THE CITY INFORMATION AND COMMUNICATIONS TECHNOLOGY MANAGEMENT OFFICER",
        "status": "REGULAR",
        "job_title": "Office Head"
    }
}


    // fetch the all office available
    http://192.168.8.182:9000/api/office/pmt/available
    [
    {
        "id": "4",
        "name": "OFFICE OF THE CITY ASSESSOR"
    },
    {
        "id": "5",
        "name": "OFFICE OF THE CITY BUDGET OFFICER"
    },
    {
        "id": "6",
        "name": "OFFICE OF THE CITY CIVIL REGISTRAR"
    },
    {
        "id": "7",
        "name": "OFFICE OF THE CITY DISASTER RISK REDUCTION AND MANAGEMENT OFFICER"
    },
    {
        "id": "8",
        "name": "OFFICE OF THE CITY ECONOMIC ENTERPRISES MANAGER"
    },
    {
        "id": "9",
        "name": "OFFICE OF THE CITY ENGINEER"
    },
   
]
]