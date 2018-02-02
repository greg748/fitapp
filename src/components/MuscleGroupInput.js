import React from 'react';
import ApiInterface from './ApiInterface';

export default class MuscleGroupInput extends React.Component {
  muscleGroups = [];
  selectedMuscle = 'Pecs';
  apiInterface = new ApiInterface();
  componentWillMount() {
    this.muscleGroups = this.apiInterface.getMuscleGroups();
    // get selected muscle from local storage
  }
  render() {
    return (
     this.muscleGroups.map((muscle) => {
       return <option key={muscle} value={muscle}>{muscle}</option>;
     }));
  }
}
