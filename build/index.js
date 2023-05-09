/******/ (function() { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "@wordpress/element":
/*!*********************************!*\
  !*** external ["wp","element"] ***!
  \*********************************/
/***/ (function(module) {

module.exports = window["wp"]["element"];

/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/compat get default export */
/******/ 	!function() {
/******/ 		// getDefaultExport function for compatibility with non-harmony modules
/******/ 		__webpack_require__.n = function(module) {
/******/ 			var getter = module && module.__esModule ?
/******/ 				function() { return module['default']; } :
/******/ 				function() { return module; };
/******/ 			__webpack_require__.d(getter, { a: getter });
/******/ 			return getter;
/******/ 		};
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/define property getters */
/******/ 	!function() {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = function(exports, definition) {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	!function() {
/******/ 		__webpack_require__.o = function(obj, prop) { return Object.prototype.hasOwnProperty.call(obj, prop); }
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	!function() {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = function(exports) {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	}();
/******/ 	
/************************************************************************/
var __webpack_exports__ = {};
// This entry need to be wrapped in an IIFE because it need to be isolated against other modules in the chunk.
!function() {
/*!**********************!*\
  !*** ./src/index.js ***!
  \**********************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);

// Import WP elements

const {
  registerBlockType
} = wp.blocks;
const {
  withSelect
} = wp.data;
const {
  CheckboxControl
} = wp.components;

// Register the block
registerBlockType('kw-test/people-block', {
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
  edit: withSelect(select => {
    // Retrieve the getEntityRecords function
    const {
      getEntityRecords
    } = select('core');

    // Return the list of 'people' custom post type objects
    const query = {
      per_page: -1,
      post_type: 'people'
    };
    const people = {
      peopleList: getEntityRecords('postType', 'people', query)
    };
    return people;
  })(PeopleBlockEdit),
  // Call PeopleBlockEdit function and pass 'people' custom post type objects

  // Save function that outputs the list of selected IDs as shortcode
  save: _ref => {
    let {
      attributes
    } = _ref;
    const {
      selectedPeople
    } = attributes;
    return selectedPeople ? `[kw-peopleblock ids="${selectedPeople.join(',')}"]` : '';
  }
}); // registerBlockType end

// Function that displays the list of 'people' posts as checkboxes
function PeopleBlockEdit(_ref2) {
  let {
    peopleList,
    attributes,
    setAttributes
  } = _ref2;
  // State variable for selected people
  const [selectedPeople, setSelectedPeople] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)([]);

  // Retrieve the selected people from the attributes on component mount
  (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => {
    const {
      selectedPeople
    } = attributes;
    if (selectedPeople) {
      setSelectedPeople(selectedPeople);
    }
  }, []);

  // Handle changes to the selectedPeople state
  function onChangeSelectedPeople(newSelected) {
    setSelectedPeople(newSelected); // Update the state
    setAttributes({
      selectedPeople: newSelected
    }); // Pass the updated state to the backend
  }

  // Check if the list of people list is empty and show a loading message
  if (!peopleList) {
    return 'Loading...';
  }

  // Map the posts to checkboxes
  const peopleCheckboxes = peopleList.map(person => {
    return (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(CheckboxControl, {
      key: person.id,
      label: person.title.rendered,
      checked: selectedPeople.includes(person.id),
      onChange: isChecked => {
        // Set checked/unchecked with add post ID in the state
        const newSelected = isChecked ? [...selectedPeople, person.id] : selectedPeople.filter(id => id !== person.id);

        // Update state
        onChangeSelectedPeople(newSelected);
      }
    });
  });
  return (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", null, peopleCheckboxes);
}
}();
/******/ })()
;
//# sourceMappingURL=index.js.map