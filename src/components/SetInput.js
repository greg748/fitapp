import React from 'react';
import ApiInterface from './ApiInterface';
import {Endpoints} from './Endpoints';

export class SetInput extends React.Component {

    endpointObj = new Endpoints();
    endpoints = this.endpointObj.endpoints();
    apiInterface = new ApiInterface;
    apiUrl = this.apiInterface.getApiUrl();
    setOptions = {
        setTypes: [],
        setUnits: []
    };

    setTypesUrl = this.apiUrl + this.endpoints.setTypes;
    fetchObject = this.apiInterface.getFetchObject('GET',
        {'Authorization': 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJmaXRhcHAubW9iaSIsImlhdCI6MTUxNjgxNTY2OSwiZXhwIjoxNTQ4MzUxNjY5LCJhdWQiOiJmaXRhcHAubW9iaSIsInN1YiI6ImdyZWdAZ3JlZ2Jvd25lLmNvbSIsIk5hbWUiOiJHcmVnIiwiU3VybmFtZSI6IkJvd25lIiwiRW1haWwiOiJncmVnQGdyZWdib3duZS5jb20iLCJSb2xlIjpbIlVzZXIiLCJBZG1pbiJdLCJ1c2VyIjoiMSJ9.pNC1qu1KW4BodSlPEp8r1ExvO3b3YvnIwuP5BoMzEEw'});

    getSetTypes() {

        const localSetTypes = this.apiInterface.apiCall(this.setTypesUrl, this.fetchObject);
        return localSetTypes;
        // localSetTypes.then(function (data) { return data; });

        // return {'bilateral': 'bilateral', 'single': 'single','alt': 'alt'};
    };

    componentWillMount() {
        let comments = this.apiInterface.apiCall('http://jsonplaceholder.typicode.com/comments',this.fetchObject);
        console.log('comments',comments);
        //this.setOptions.setTypes = comments;
    }



    // try the whole thing with http://jsonplaceholder.typicode.com/comments

    render () {
        return (<div>
                {/*{
                    this.setOptions.setTypes && Object.values(this.setOptions.setTypes)
                        .forEach(function(value,i) {
                             return <p key={i}>{value}</p>
                            }
                        )
                }*/}
        </div>);
    }

}

export default SetInput;