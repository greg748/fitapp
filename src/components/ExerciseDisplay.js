import React from 'react';
import SetInfo from './SetInfo';

class ExerciseDisplay extends React.Component {
    constructor(props) {
        super(props);
        this.exerciseInfo = props.exerciseInfo;
        this.groupOrdinal = props.groupOrdinal;
        if (!this.exerciseInfo.sets) {
            this.exerciseInfo.sets = [{}];
        }
        this.state = {
            sets :this.exerciseInfo.sets
        }
    };

    processSetChange(setInfo) {
        console.log('processSetChange', setInfo);
    }

    render() {
        return (
            <div className="exercise">
        <div className="exerciseName">{this.groupOrdinal} : {this.exerciseInfo.name}</div>
        { this.state.sets.map((set, i) => (
            <SetInfo key={i} groupOrdinal={this.groupOrdinal}
                     exercise={this.exerciseInfo.exercise_id}
                     set={i+1} setInfo={set}
                     processSetChange = {this.processSetChange}
            />
        ))
        }
            </div>
    )
    };

}

export default ExerciseDisplay;