// Import WP elements
import { useState, useEffect } from '@wordpress/element'; 
const { registerBlockType } = wp.blocks;
const { withSelect } = wp.data;
const { CheckboxControl } = wp.components;

// Register the block
registerBlockType( 'kw-test/people-block', {
  title: 'People Block',
  icon: 'groups',
  category: 'widgets',

  // Set attribute of selected
  attributes: {
    selectedPeople: {
        type: 'array',
        default: []
    }
  },

  // Retrieve the list of people from the backend
  edit: withSelect( ( select ) => {

    // Retrieve the getEntityRecords function
    const { getEntityRecords } = select( 'core' ); 

    // Return the list of 'people' custom post type objects
    const query = { per_page: -1, post_type: 'people'};
    const people = { peopleList: getEntityRecords( 'postType', 'people', query ) };
    return people;
  } )( PeopleBlockEdit ), // Call PeopleBlockEdit function and pass 'people' custom post type objects

  // Save function that outputs the list of selected IDs as shortcode
  save: ( { attributes } ) => {
    const { selectedPeople } = attributes;
    return selectedPeople ? `[kw-peopleblock ids="${selectedPeople.join(',')}"]` : '';
  },

}); // registerBlockType end


// Function that displays the list of 'people' posts as checkboxes
function PeopleBlockEdit( { peopleList, attributes, setAttributes } ) {

  // State variable for selected people
  const [selectedPeople, setSelectedPeople] = useState( [] ); 

  // Retrieve the selected people from the attributes on component mount
  useEffect( () => {
    const { selectedPeople } = attributes;
    if ( selectedPeople ) {
      setSelectedPeople( selectedPeople );
    }
  }, [] );

  // Handle changes to the selectedPeople state
  function onChangeSelectedPeople( newSelected ) {
    setSelectedPeople( newSelected ); // Update the state
    setAttributes( { selectedPeople: newSelected } ); // Pass the updated state to the backend
  }

  // Check if the list of people list is empty and show a loading message
  if ( !peopleList ) {
    return 'Loading...';
  }

  // Map the posts to checkboxes
  const peopleCheckboxes = peopleList.map( ( person ) => {

    return (
      <CheckboxControl

        key={person.id}
        label={person.title.rendered} 
        checked={selectedPeople.includes( person.id )}

        onChange={( isChecked ) => {

            // Set checked/unchecked with add post ID in the state
            const newSelected = isChecked ? [ ...selectedPeople, person.id ] : selectedPeople.filter( ( id ) => id !== person.id );

            // Update state
            onChangeSelectedPeople( newSelected );
          }
        }

      />
    );
    
  });

  return (
    <div>
      {peopleCheckboxes}
    </div>
  );
}
