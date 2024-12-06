<template>
  <div class="wrapper">
    <!-- Add New Item with Autocomplete and Right-aligned Button -->
    <div class="add-item">
        <div class="input-container">
        <input
            v-model="newItem"
            type="text"
            placeholder="Add a new item..."
            @input="filterSuggestions"
            @keyup.enter="addItem"
        />
        <button class="add-button" @click="addItem">Add</button>
        </div>

        <!-- Autocomplete suggestions -->
        <ul v-if="filteredSuggestions.length" class="autocomplete-list">
        <li
            v-for="suggestion in filteredSuggestions"
            :key="suggestion"
            @click="selectSuggestion(suggestion)"
            class="autocomplete-item"
        >
            {{ suggestion }}
        </li>
        </ul>
    </div>
    <!-- Unchecked Items -->
    <h2 class="subtitle">Items to buy</h2>
    <ul class="item-list">
        <li v-for="item in uncheckedItems" :key="item" class="item">
        <label>
            <input type="checkbox" @change="checkItem(item, true)" />
            <span>{{ formatItem(item) }}</span>
        </label>
        </li>
    </ul>

    <!-- Divider -->
    <hr />

    <!-- Toggle Button for Checked Items -->
    <button class="toggle-button" @click="toggleCheckedItems">
        {{ showCheckedItems ? 'Hide' : 'Show' }} Bought Items
    </button>

    <!-- Checked Items Section (Collapsible) -->
    <div v-if="showCheckedItems">
        <h2 class="subtitle">Bought Items</h2>
        <ul class="item-list">
        <li v-for="item in checkedItems" :key="item" class="item checked">
            <label>
            <input type="checkbox" @change="checkItem(item, false)" checked />
            <span>{{ formatItem(item) }}</span>
            </label>
        </li>
        </ul>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import api from 'cookbook/js/api-interface';
import { useStore } from '../store';
import {
    onBeforeRouteLeave,
    onBeforeRouteUpdate,
    useRoute,
} from 'vue-router/composables';

/**
 * @type {import('vue').Ref<boolean>}
 */
const isLoading = ref(false);

const store = useStore();

const setup = async () => {
    isLoading.value = true;
    store.dispatch('setPage', { page: 'shopping-list' });

    const response = api.shoppingList.list();
    console.debug(response);
};

// ===================
// Vue lifecycle
// ===================

onBeforeRouteUpdate((to, from, next) => {
    // beforeRouteUpdate is called when the static route stays the same
    next();

    // Check if we should reload the component content
    if (helpers.shouldReloadContent(from.fullPath, to.fullPath)) {
        setup();
    }
});
setup();
</script>

<script>
export default {
  data() {
    return {
      items: [],
      newItem: '',
      showCheckedItems: true, // Control the visibility of the Checked Items section
      filteredSuggestions: [], // For autocomplete suggestions
    };
  },
  computed: {
    uncheckedItems() {
      return this.items.filter(item => item.includes('[ ]'));
    },
    checkedItems() {
      return this.items.filter(item => item.includes('[x]'));
    },
    checkedItemsFormatted() {
      return this.checkedItems.map(item => this.formatItem(item));
    },
  },
  methods: {
    fetchItems() {
      response = api.shoppingList.list();
      console.debug(response);
    },
    addItem() {
      if (!this.newItem) return;
      fetch('http://nextcloud-recipe.local/api.php', { // Updated API URL
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({item: this.newItem}),
      }).then(this.fetchItems);
      this.newItem = '';
      this.filteredSuggestions = []; // Clear suggestions after adding the item
    },
    checkItem(item, checked) {
      fetch('http://nextcloud-recipe.local/api.php', { // Updated API URL
        method: 'PUT',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({item: item, checked: checked}),
      }).then(this.fetchItems);
    },
    toggleCheckedItems() {
      this.showCheckedItems = !this.showCheckedItems;
    },
    formatItem(item) {
      return item.replace('- [ ]', '').replace('- [x]', '');
    },
    filterSuggestions() {
      // Filter checked items based on the newItem input value
      if (this.newItem) {
        this.filteredSuggestions = this.checkedItemsFormatted.filter(item =>
            item.toLowerCase().includes(this.newItem.toLowerCase())
        );
      } else {
        this.filteredSuggestions = [];
      }
    },
    selectSuggestion(suggestion) {
      // Uncheck the selected suggestion and move it back to the unchecked list
      const itemToUncheck = this.items.find(item => this.formatItem(item) === suggestion && item.includes('[x]'));
      if (itemToUncheck) {
        this.checkItem(itemToUncheck, false); // Uncheck the item (move to unchecked)
      }
      this.newItem = ''; // Clear input after selecting the suggestion
      this.filteredSuggestions = []; // Clear suggestions after selecting
    },
  },
  mounted() {
    //this.fetchItems();
  },
};
</script>

<style lang="scss">
.wrapper {
    width: 100%;
    padding: 1rem;
}

.card {
  background: #fff;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  margin-bottom: 20px;
}

.card-header {
  background: #f0f0f0;
  padding: 16px;
  border-bottom: 1px solid #e0e0e0;
}

.title {
  font-size: 24px;
  color: #333;
}

.subtitle {
  font-size: 18px;
  margin-top: 20px;
  color: #555;
}

.card-body {
  padding: 16px;
}

.item-list {
  list-style: none;
  padding: 0;
  margin: 0;
}

.item {
  margin: 10px 0;
  font-size: 16px;
}

.item label {
  display: flex;
  align-items: center;
}

.item input[type='checkbox'] {
  margin-right: 10px;
}

.checked span {
  text-decoration: line-through;
  color: #888;
}

.add-item {
  position: relative;
  margin-top: 20px;
}

.input-container {
  display: flex;
  position: relative;
}

.add-item input {
  flex: 1;
  padding: 10px;
  font-size: 16px;
  border: 1px solid #ccc;
  border-radius: 4px 0 0 4px;
}

.add-button {
  background-color: #007bff;
  color: white;
  padding: 10px 20px;
  border: none;
  border-radius: 0 4px 4px 0;
  cursor: pointer;
  transition: background-color 0.2s;
}

.add-button:hover {
  background-color: #0056b3;
}

.autocomplete-list {
  list-style: none;
  padding: 0;
  margin: 5px 0 0 0;
  background: white;
  border: 1px solid #ccc;
  border-radius: 4px;
  position: absolute;
  top: 45px;
  width: 100%;
  z-index: 10;
}

.autocomplete-item {
  padding: 10px;
  cursor: pointer;
}

.autocomplete-item:hover {
  background-color: #f0f0f0;
}

.toggle-button {
  padding: 10px 20px;
  background-color: #28a745;
  color: white;
  border: none;
  border-radius: 4px;
  margin-bottom: 10px;
  cursor: pointer;
}

.toggle-button:hover {
  background-color: #218838;
}
</style>