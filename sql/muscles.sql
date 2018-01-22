insert into muscles (name,region,workout_type) values 
('abdominals','core','{1,2,3,4,5,6,7}'),
('adductors','leg','{1,3}'),
('biceps','arm','{1,2,4}'),
('deltoids','shoulders','{1,2,6}'),
('erector spinae','back','{1,2,3,4,5,6,7}'),
('gastroc/soleus','leg','{1,3}'),
('hamstrings','leg','{1,3}'),
('lats','back','{1,2,5,6}'),
('obliques','core','{1,2,3,4,5,6,7}'),
('pectorals','chest','{1,2,4}'),
('quads','leg','{1,3}'),
('rhomboids','back','{1,2,5,6}'),
('trapezius','back','{1,2,5,6}'),
('triceps','arm','{1,2,5}'),
('glutes','leg','{1,3}'),
('serratus','core','{1,2,5,7}');

insert into muscles (name,region,workout_type) values 
('deltoid-posterior','shoulders','{1,2,6}'),
('deltoid-anterior','shoulders','{1,2,6}'),
('glute meds','leg','{1,3}');
update muscles set name='deltoid-lateral' where name='deltoid';