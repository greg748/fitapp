import React from 'react';

class ApiInterface {
    muscleGroups = ['Select','Pecs','Abs','Obliques','Glutes','Hamstrings'];

    getWorkoutTypes = () => {
        return ['Full Body','Upper Body','Lower Body'];
    };
    getMuscleGroups = () => {
        return this.muscleGroups;
    };

    getApiUrl = () => {
        return 'http://localhost:82';
    };

    getFetchObject = (method = 'GET', headers = {}, body = {}) => {
        let fetchObject = {};
        if (method == 'GET') {
            fetchObject = {
                method : method,
                headers : headers
            }
        } else {
            fetchObject = {
                method : method,
                headers : headers,
                body : body
            }
        }
        return fetchObject;
    };

    apiCall(url, fetchObject, callback) {
        fetch(url, fetchObject)
            .then(function (res) {
                return res.json();
            }).then(function (resJson) {
                console.log(url,resJson);
                callback(resJson);
            //return resJson;
        })
            .catch(function (error) {
                console.log(error);
            });

    }

}

export default ApiInterface;