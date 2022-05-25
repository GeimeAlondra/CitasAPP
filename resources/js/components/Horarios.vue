<template>
  <div class="content">
    <div class="md-layout-item md-medium-size-100 md-xsmall-size-100 md-size-100">
      <v-overlay :value="loader" :z-index="'99999999'">
        <v-progress-circular indeterminate size="80" color="grey darken-4"></v-progress-circular>
      </v-overlay>
      <v-card>
        <v-card-title>
          Listado de horarios disponibles
          <div class="flex-grow-1"></div>
          <v-text-field v-model="search" label="Buscar horario" hide-details></v-text-field>
        </v-card-title>
        <v-data-table
          :headers="hTBHorarios"
          :items="arrayHorarios"
          :footer-props="{
            'items-per-page-options': [5,10, 20, 30,40],
            'items-per-page-text' : 'Registros por página'
          }"
          :items-per-page="5"
          :search="search"
          multi-sort
          class="elevation-1"
        >
          <!-- Template Para Modal de Actualizar y Agregar Horario -->

          <template v-slot:top>
            <v-toolbar flat color="white">
              <div class="flex-grow-1"></div>
              <v-dialog v-model="dialog" persistent max-width="700px">
                <template v-slot:activator="{ on }">
                  <v-btn elevation="10" color="grey darken-3" dark class="mb-2" v-on="on">
                    Agregar&nbsp;
                    <v-icon>mdi-plus-box-multiple-outline</v-icon>
                  </v-btn>
                </template>
                <v-card>
                  <v-card-title class="headline grey lighten-2" primary-titles>
                    <span class="headline" v-text="formTitle"></span>
                  </v-card-title>
                  <v-card-text>
                    <v-container>
                      <v-form ref="formHorario" v-model="validForm" :lazy-validation="true">
                        <v-text-field
                          append-icon="mdi-folder-outline"
                          v-model="horario.hora"
                          @keyup="errorsHora = []"
                          :rules="[v => !!v || 'Hora es requerida']"
                          label="Hora"
                          required
                          :error-messages="errorsHora"
                        ></v-text-field>
                        
                      </v-form>
                    </v-container>
                  </v-card-text>
                  <v-divider></v-divider>
                  <v-card-actions>
                    <div class="flex-grow-1"></div>
                    <v-btn color="red darken-1" text @click="cerrarModal">Cerrar</v-btn>
                    <v-btn
                      color="info darken-1"
                      :disabled="!validForm"
                      @click="saveHorario()"
                      text
                      v-text="btnTitle"
                    ></v-btn>
                  </v-card-actions>
                </v-card>
              </v-dialog>
            </v-toolbar>
          </template>
         
          <!-- Template que va en la tabla en la columna de Acciones (Editar,Desactivar) -->
        
          <template v-slot:item.action="{item}" v-slot:activator="{ on }">
            <v-tooltip top>
              <template v-slot:activator="{ on }">
                <v-btn
                  color="success"
                  elevation="8"
                  small
                  dark
                  :disabled="item.id < 0"
                  v-on="on"
                  @click="showModalEditar(item)"
                >
                  <v-icon>mdi-pencil</v-icon>
                </v-btn>
              </template>
              <span>Actualizar datos</span>
            </v-tooltip>
            <v-tooltip top >
              <template v-slot:activator="{ on }" >
                <v-btn
                  color="info"
                  class="mx-1"
                  elevation="8"
                  small
                  dark
                  :disabled="item.id < 0"
                  v-on="on"
                  @click="deleteHorario(item)"
                >
                  <v-icon>mdi-delete</v-icon>
                </v-btn>
              </template>
              <span>Eliminar horario</span>
            </v-tooltip>
          </template>
        </v-data-table>
        <v-snackbar v-model="snackbar">
          {{ msjSnackBar }}
          <v-btn color="red" text @click="snackbar = false">Cerrar</v-btn>
        </v-snackbar>
      </v-card>
    </div>
  </div>
</template>
<script>
export default {
  data() {
     return {
      arrayHorarios: [],
      hTBHorarios: [
        { text: "Hora", value: "hora" },
        { text: "Acciones", value: "action", sortable: false, align: "center" }
      ],
      loader: false,
      search: "",
      dialog: false,
      horario: {
        id: null,
        hora: ""
      },
      validForm: true,
      snackbar: false,
      msjSnackBar: "",
      errorsHora: [],
      editedHorario: -1
    };
  },
  computed: {
    formTitle() {
      return this.horario.id === null
        ? "Agregar horario"
        : "Actualizar horario";
    },
    btnTitle() {
      return this.horario.id === null ? "Guardar" : "Actualizar";
    }
  },
  methods: {
    
    fetchHorarios() {
      let me = this;
      me.loader = true;
      axios.get(`/horarios/all`)
        .then(function(response) {
          me.arrayHorarios = response.data;
          me.loader = false;
        })
        .catch(function(error) {
          me.loader = false;
          console.log(error);
        });
      
     me.loader = false;
    },
    
    setMessageToSnackBar(msj, estado) {
      let me = this;
      me.snackbar = estado;
      me.msjSnackBar = msj;
    },
    cerrarModal() {
      let me = this;
      me.dialog = false;
      setTimeout(() => {
        me.horario = {
          id: null,
          hora: ""
        };
        me.resetValidation();
      }, 300);
    },
    resetValidation() {
      let me = this;
      me.errorsHora = [];
      me.$refs.formHorario.resetValidation();
    },
    showModalEditar(horario) {
      let me = this;
      me.editedHorario = me.arrayHorarios.indexOf(horario);
      me.horario = Object.assign({}, horario);
      me.dialog = true;
    },
   saveHorario() {
      let me = this;
      if (me.$refs.formHorario.validate()) {
        let accion = me.horario.id == null ? "add" : "upd";
        me.loader = true;
        if(accion=="add"){
            axios.post(`/horarios/save`,me.horario)
            .then(function(response) {
             console.log(response.status);
              if(response.status ==201){
                 me.verificarAccionDato(response.data, response.status, accion);
                 me.cerrarModal(); 
                 console.log(response.status);
              }else{
                Vue.swal("Error", "Ocurrio un error, intente de nuevo", "error");
                me.cerrarModal();
              }
            
            })
            .catch(function(error){
               Vue.swal("Error", "Ocurrio un error, intente de nuevo", "error");
            });
            me.loader = false;
        }else{
            //Para actualizar
                axios.put(`/horarios/update`,me.horario)
               .then(function(response) {
                 if(response.status==202){
                    me.verificarAccionDato(response.data, response.status, accion);
                        me.cerrarModal();  
                 }else{
                    Vue.swal("Error", "Ocurrio un error, intente de nuevo", "error");
                     me.cerrarModal();
                 }
              })
          .catch(function(error) {
            console.log(error);
            me.loader = false;
          });
        }
      }
    },
    deleteHorario(horario) {
      let me = this;
      me.editedHorario = me.arrayHorarios.indexOf(horario);
      
      const Toast = Vue.swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        onOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer)
            toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
        });
        //Personalizando nueva confirmacion
        Vue.swal.fire({
        title: 'Eliminar Horario',
        text: "Una vez realizada la acción no se podra revertir",
        type: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Si',
        cancelButtonText: "No"
        }).then((result) => {
        if (result.value) {
            me.loader = true;
            axios.post(`/horarios/delete`, horario)
            .then(function(response) {
                me.verificarAccionDato(response.data, response.status, "del");
                me.loader = false;
            })
          }
        });
    },
     verificarAccionDato(horario, statusCode, accion) {
      let me = this;
      
      const Toast = Vue.swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        onOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer)
            toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
        }); 
      switch (accion) {
        case "add":
          //Agrego al array de horarios el objecto que devuelve el Backend
          this.fetchHorarios(); 
          Toast.fire({
            icon: 'success',
            title: 'Hora registrada con exito'
           });
          me.loader = false;
          break;
        case "upd":
          //Actualizo al array de horarios el objecto que devuelve el backend ya con los datos actualizados
          this.fetchHorarios(); 
          Toast.fire({
            icon: 'success',
            title: 'Hora actualizada con exito'
           }); 
            
          me.loader = false;
          break;
        case "del":
          if (statusCode == 200) {
            try {
              //Se elimina del array de Horarios activos si todo esta bien en el backend
              me.arrayHorarios.splice(me.editedHorario, 1);
              Toast.fire({
                icon: 'success',
                title: 'Hora eliminada'
              });
            } catch (error) {
              console.log(error);
            }
          } else {
             Toast.fire({
                icon: 'error',
                title: 'Ocurrió un error, intente de nuevo'
              });
          }
          break;
      }
    }
  },
  mounted() {
    let me = this;
    me.fetchHorarios();
  }
};
</script>