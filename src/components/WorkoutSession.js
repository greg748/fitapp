import React from 'react';
import ExerciseGroup from './ExerciseGroup';
import ApiInterface from './ApiInterface';
import {Endpoints} from './Endpoints';

export class WorkoutSession extends React.Component {

    constructor (props) {
        super(props);
        this.workoutInstance = {
            name : 'Upper Body 1/15/2016',
            created : '2016-01-15',
            groups : [
            {
                type: 'warmup',
                exercises: [{exercise_id: 44, name: 'push-up', sets : [{type:'single',reps: 12},{type:'bilateral', reps: 10}]},
                    {exercise_id: 45, name: 'sit-up'},
                    { exercise_id: 46, name: 'plank'}
                    ]
            }
            , {
            type: 'main',
            exercises: [{exercise_id: 27, name: 'chin-up'},
                {exercise_id: 2, name: 'chest press'},
                { exercise_id: 18, name: 'lateral raises'}
                ]
             }]
        };
        this.apiInterface = new ApiInterface;
        this.apiUrl = this.apiInterface.getApiUrl();
        this.endpointObj = new Endpoints();
        this.endpoints = this.endpointObj.endpoints();

        this.workoutInformationUrl = this.apiUrl + this.endpoints.workoutSession + '43';
        this.fetchObject = this.apiInterface.getFetchObject('GET',
            {'Authorization': 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJmaXRhcHAubW9iaSIsImlhdCI6MTUxNjgxNTY2OSwiZXhwIjoxNTQ4MzUxNjY5LCJhdWQiOiJmaXRhcHAubW9iaSIsInN1YiI6ImdyZWdAZ3JlZ2Jvd25lLmNvbSIsIk5hbWUiOiJHcmVnIiwiU3VybmFtZSI6IkJvd25lIiwiRW1haWwiOiJncmVnQGdyZWdib3duZS5jb20iLCJSb2xlIjpbIlVzZXIiLCJBZG1pbiJdLCJ1c2VyIjoiMSJ9.pNC1qu1KW4BodSlPEp8r1ExvO3b3YvnIwuP5BoMzEEw'});

        this.state = {
            workoutInstance: false,
            groups: false
        }

    }

    setWorkoutInstanceFromAPI = (data) => {
        this.setState({workoutInstance : data});
        this.setState({groups : data.groups});
        Object.keys(data.groups).map((exerciseGroup, i) => {
            <ExerciseGroup key={i} groupOrdinal={i+1} exerciseGroup={exerciseGroup} />
        }
        );
        console.log(data.groups);
    }

    componentWillMount() {
        this.apiInterface.apiCall(this.workoutInformationUrl, this.fetchObject, this.setWorkoutInstanceFromAPI);

    }

    render() {
        return (

            <div>
                <h2>Workout: {!!this.state.workoutInstance && this.state.workoutInstance.name}</h2>
                //    {}
                //         this.state.groups &&
                //             for (exerciseGroup in this.state.groups) {
                //
                //              <ExerciseGroup key={i} groupOrdinal={i+1} exerciseGroup={exerciseGroup} />
                //
                //         }
                //     }
            </div>

        )

    }
}

