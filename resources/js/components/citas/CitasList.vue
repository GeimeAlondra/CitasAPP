<template>
  <div class="content">
    <div class="md-layout-item md-medium-size-100 md-xsmall-size-100 md-size-100">
      <v-overlay :value="loader" :z-index="'99999999'">
        <v-progress-circular indeterminate size="80" color="grey darken-4"></v-progress-circular>
      </v-overlay>
      <v-card>
        <v-card-title>
          {{titleList}}
          <div class="flex-grow-1"></div>
          <v-text-field v-model="search" label="Buscar" hide-details></v-text-field>
        </v-card-title>
        <v-data-table
          :headers="hTBCitas"
          :items="arrayCitas"
          :footer-props="{
            'items-per-page-options': [5,10, 20, 30,40],
            'items-per-page-text' : 'Registros Por Página'
          }"
          :items-per-page="10"
          :search="search"
          multi-sort
          class="elevation-1"
        >
          <template v-slot:top>
            <v-toolbar flat color="white">
                <v-checkbox v-model="mostrarCitas" class="mx-6" label="Citas activas" >value="false"</v-checkbox>
                <v-checkbox v-model="mostrarAnuladas" class="mx-6" label="Citas anuladas" >value="false"</v-checkbox>
              <div class="flex-grow-1"></div>

            </v-toolbar>
          </template>
         
          <!-- Template para las acciones-->
        
          <template v-slot:item.action="{item}">
            <v-tooltip top >
              <template v-slot:activator="{ on }" >
                <v-btn v-if="estado=='R'"
                elevation="10"
                color="success"
                class="white--text"
                small
                dark  
                @click="cambiarEstado(item,'C')"                 
                v-on="on"                  
                >
                  <v-icon>check</v-icon>
                </v-btn>
              </template>
              <span>Realizar cita</span>
            </v-tooltip>
            <v-tooltip top >
              <template v-slot:activator="{ on }" >
                <v-btn v-if="estado=='R'"
                  color="red"
                  class="mx-1 white--text"
                  elevation="8"
                  small                 
                  v-on="on" 
                  @click="cambiarEstado(item,'A')"                 
                >
                  <v-icon>close</v-icon>
                </v-btn>
              </template>
              <span>Anular cita</span>
            </v-tooltip>
            
             <v-tooltip top >
              <template v-slot:activator="{ on }" >
                <v-btn v-if="estado=='A'"
                  color="deep-orange"
                  class="mx-1 white--text"
                  elevation="8"
                  small                 
                  v-on="on" 
                  @click="cambiarEstado(item,'R')"                  
                >
                  <v-icon>mdi-catched</v-icon>
                </v-btn>
              </template>
              <span>Devolver cita</span>
            </v-tooltip>

             <v-tooltip top >
              <template v-slot:activator="{ on }" >
                <v-btn v-if="estado=='C'"
                  color="deep-orange"
                  class="mx-1 white--text"
                  elevation="8"
                  small                 
                  v-on="on" 
                  @click="cambiarEstado(item,'R')"                  
                >
                  <v-icon>mdi-catched</v-icon>
                </v-btn>
              </template>
              <span>Devolver cita</span>
            </v-tooltip>

          </template>
        </v-data-table>
      </v-card>
    </div>
  </div>
</template>
<script>
export default {
    name: "ListadoCitas",
  data: () => ({
      arrayCitas: [],
      hTBCitas: [
        { text: "Paciente", value: "user" },
        { text: "Fecha", value: "fecha" },
        { text: "Horario", value: "horario" },
        { text: "Dentista", value: "dentista" },

        { text: "Acciones", value: "action", sortable: false, align: "center" }
      ],
      loader: false,
      search: "",
      estado:"R",
      mostrarCitas:false,
      mostrarAnuladas:false,
     
      mes: null,
      anio: null,
      titleList:"Listado de reserva de citas"
  }),
  
  watch:{
      mostrarCitas(){
          let me = this;
          if(me.mostrarCitas) {
              me.estado = 'C';
              me.titleList = "Listado de citas activas"
          }else{
              me.estado = 'R';
              me.titleList = "Listado de citas"
          }
          me.fetchCitas(me.estado);
      },
      mostrarAnuladas(){
          let me = this;
          if(me.mostrarAnuladas) {
              me.estado = 'A';
              me.titleList = "Listado de citas anuladas"
          }else{
              me.estado = 'R';
              me.titleList = "Listado de citas"
          }
          me.fetchCitas(me.estado);
      },
     
  },
  computed: {},
  methods: {
    
    fetchCitas() {
      let me = this;
      me.loader = true;
        axios.get(`/citas/state?state=`+me.estado)
        .then(function(response) {
         if(Object.keys(response.data).length==0){
             me.arrayCitas = [];
         }else{
             me.arrayCitas = response.data;
         }
          me.loader = false;
        })
        .catch(function(error) {
          me.loader = false;
          console.log(error);
        });
      
     me.loader = false;
    },
    
    cambiarEstado(cita,estado) {
        let me = this;
        cita.estado = estado;
    const Toast = Vue.swal.mixin({
        toast: true,
        position: "bottom-end",
        showConfirmButton: true,
        timer: 8000
    });

    Vue.swal({
        title: "¿Esta seguro/a de realizar esta acción?",
        text:
        "Una vez realizada la operación, el registro desaparecerá de la tabla",
        type: "question",
        showCancelButton: true,
        confirmButtonText: "Si",
        cancelButtonText: "No",
        reverseButtons: true,
        focusConfirm: false,
        focusCancel: true,
        showCloseButton: true
    }).then(result => {
        if(result.value) {
            me.loader = true;
            axios.put(`/citas/change`, cita)
            .then(function(response) {
                me.loader = false;
                if(response.status == 200) {
                Vue.swal(
                    "Confirmado",
                    "La operación ha sido realizada con exito",
                    "success"
                );
                let estado = "R";
                me.fetchCitas(estado);
            }
            })
            .catch(function(error) {
            console.log(error);
            me.loader = false;
            Toast.fire({
                type: "error",
                title: "Ocurrió un error, intente nuevamente"
            });
        });
    }
});
}   
},
  mounted() {
    let me = this;
    me.fetchCitas(me.estado);
  }
};
</script>