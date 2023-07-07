<script>
import DemoSimpleTableBasics from "../views/pages/tables/DemoSimpleTableBasics.vue";
export default {
  data() {
    return {
      itemsPerPage: 10,
      searchQuery: "",
      candidatesType: [],
      selectedType: "",
      headers: [
        {
          title: "ID",
          align: "start",
          key: "Id",
        },
        { title: "Name", align: "start", key: "Name" },
      ],
      candidates: [
        {
          id: 1,
          name: "samir",
          type: "",
        },
      ],
    };
  },
  components: { DemoSimpleTableBasics },
};
</script>
<template>
  <div class="container">
    <v-row>
      <v-col cols="8">
        <h2>Candidates</h2>
      </v-col>
    </v-row>
    <v-row>
      <v-col>
        <v-card>
          <v-card-title>
            <v-row>
              <v-text-field
                v-model="searchQuery"
                label="Search"
                single-line
                hide-details
              ></v-text-field>
              <v-btn
                icon="mdi-magnify"
                variant="plain"
                @click="searchCandidate"
              ></v-btn>
            </v-row>
          </v-card-title>
          <DemoSimpleTableBasics />
        </v-card>
      </v-col>
    </v-row>
  </div>
</template>

 <style>
.container {
  padding: 20px;
}
</style>

