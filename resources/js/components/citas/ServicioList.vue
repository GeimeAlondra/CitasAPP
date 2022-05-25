<template>
  <v-container>
    <v-row>
      <div class="flex-grow-1"></div>
      <v-text-field v-model="search" label="Buscar Servicio" hide-details />
      <v-col cols="12" md="12" sm="12" lg="12">
        <v-data-table
          :headers="hTBServicios"
          :items.sync="arrayServicios"
          :items-per-page="10"
          :footer-props="{
            'items-per-page-options': [5, 10, 15, 20],
          }"
          :search="search"
          :single-select="false"
          show-select
          v-model="item"
          :dense="true"
          multi-sort
        >
        </v-data-table>
      </v-col>
    </v-row>
  </v-container>
</template>
<script>
export default {
  data: () => ({
    item: {},
    arrayServicios: [],
    hTBServicios: [
      { text: "Descripción", value: "descripcion" },
    ],

    loader: false,
    modalServicios: false,
    errorsNombre: [],
    search: "",
    editedServicio: -1,
    alert: false,
    ServiciosCb: true,
    loadData: false,
  }), 
  watch: {
    item: function () {
      this.$emit("added", this.item);
    },
  },
  computed: {},
  methods: {
    fetchServicios() {
      let me = this;
      return axios.get(`/servicios/all`);
    },
    fetchData() {
      let me = this;
      me.loader = true;
      axios.all([me.fetchServicios()])
        .then(
            axios.spread((servicios) => {
            me.arrayServicios = servicios.data;
            me.loader = false;
          })
        )
        .catch(function (error) {
          console.log(error);
          me.loader = false;
          Vue.swal("Error", "Ocurrió un error, intente nuevamente", "error");
        });
    },
  },
  components: {},
  mounted() {
    let me = this;
    me.fetchData();
    me.$emit("created");
  },
};
</script>
