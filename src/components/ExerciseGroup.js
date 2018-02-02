import React from 'react';
import ExerciseDisplay from './ExerciseDisplay';

class ExerciseGroup extends React.Component {
    constructor(props) {
        super(props);
        this.exerciseGroup = props.exerciseGroup;
        this.groupOrdinal = props.groupOrdinal;
    };

    render() {
      return (
          <div>
          <h3>{this.groupOrdinal} {this.exerciseGroup.type}</h3>
        {
            this.exerciseGroup.exercises.map((exercise, i)=>
                (<ExerciseDisplay key={i} groupOrdinal={this.groupOrdinal} exerciseInfo={exercise}/>))
        }
          </div>
      )
    };
}

export default ExerciseGroup;