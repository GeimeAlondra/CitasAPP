<template>
  <div class="content">
    <div class="md-layout-item md-medium-size-100 md-xsmall-size-100 md-size-100">
      <v-overlay :value="loader" :z-index="'99999999'">
        <v-progress-circular indeterminate size="80" color="grey darken-4"></v-progress-circular>
      </v-overlay>
      <v-card>
        <v-card-title>
          Listado de pacientes
          <div class="flex-grow-1"></div>
          <v-text-field v-model="search" label="Buscar paciente" hide-details></v-text-field>
        </v-card-title>
        <v-data-table
          :headers="hTBPacientes"
          :items="arrayPacientes"
          :footer-props="{
            'items-per-page-options': [5,10, 20, 30,40],
            'items-per-page-text' : 'Registros por página'
          }"
          :items-per-page="5"
          :search="search"
          multi-sort
          class="elevation-1"
        >
          <!-- Template Para Modal de Actualizar y Agregar Paciente -->

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
                      <v-form ref="formPaciente" v-model="validForm" :lazy-validation="true">
                         <v-text-field
                          append-icon="mdi-folder-outline"
                          v-model="paciente.nombre"
                          @keyup="errorsNombre = []"
                          :rules="[v => !!v || 'Nombre es requerido']"
                          label="Nombre"
                          required
                          :error-messages="errorsNombre"
                        ></v-text-field>
           <v-row>
              <v-col cols="12" md="12"><b>Fecha de nacimiento</b></v-col>
              <v-col cols="12" md="4">
                <date-picker
                  v-model="paciente.fecha_nacimiento"
                  :editable="false"
                  lang="es"
                  value-type="format"
                  format="YYYY-MM-DD"
                  class="mt-3"
                  style="width: 100% !important"
                  input-class="form-control"
                ></date-picker>
              </v-col>
            </v-row>
                         <v-text-field
                          append-icon="laptop"
                          v-model="paciente.correo"
                          @keyup="errorsNombre = []"
                          :rules="[v => !!v || 'Correo es requerido']"
                          label="Correo electrónico"
                          required
                          :error-messages="errorsNombre"
                        ></v-text-field>
                         <v-text-field
                          append-icon="phone"
                          v-model="paciente.telefono"
                          @keyup="errorsNombre = []"
                          :rules="[v => !!v || 'Telefono es requerido']"
                          label="Teléfono"
                          required
                          :error-messages="errorsNombre"
                        ></v-text-field>
                         <v-text-field
                          append-icon="mdi-folder-outline"
                          v-model="paciente.passwd"
                          @keyup="errorsNombre = []"
                          :rules="[v => !!v || 'Contraseña es requerida']"
                          label="Contraseña"
                          required
                          :error-messages="errorsNombre"
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
                      @click="savePaciente()"
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
                  @click="deletePaciente(item)"
                >
                  <v-icon>mdi-delete</v-icon>
                </v-btn>
              </template>
              <span>Eliminar paciente</span>
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
      arrayPacientes: [],
      hTBPacientes: [
        { text: "Nombre", value: "nombre" },
        { text: "Fecha de nacimiento", value: "fecha_nacimiento" },
        { text: "Correo electrónico", value: "correo" },
        { text: "Teléfono", value: "telefono" },
        //{ text: "Contraseña", value: "passwd" },
        { text: "Acciones", value: "action", sortable: false, align: "center" }
      ],
      loader: false,
      search: "",
      dialog: false,
      paciente: {
        id: null,
        nombre: "",
        fecha_nacimiento: null,
        correo: null,
        telefono: null,
        //passwd: null
      },
      validForm: true,
      snackbar: false,
      msjSnackBar: "",
      errorsNombre: [],
      editedPaciente: -1
    };
  },
  computed: {
    formTitle() {
      return this.paciente.id === null
        ? "Agregar paciente"
        : "Actualizar paciente";
    },
    btnTitle() {
      return this.paciente.id === null ? "Guardar" : "Actualizar";
    }
  },
  methods: {
    fetchPacientes() {
      let me = this;
      me.loader = true;
      axios.get(`/pacientes/all`)
        .then(function(response) {
          me.arrayPacientes = response.data;
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
        me.paciente = {
          id: null,
          nombre: "",
          fechaNacimiento: null,
          correo: null,
          telefono: null,
          passwd: null
        };
        me.resetValidation();
      }, 300);
    },
    resetValidation() {
      let me = this;
      me.errorsNombre = [];
      me.$refs.formPaciente.resetValidation();
    },
    showModalEditar(paciente) {
      let me = this;
      me.editedPaciente = me.arrayPacientes.indexOf(paciente);
      me.paciente = Object.assign({}, paciente);
      me.dialog = true;
    },
    
   savePaciente() {
      let me = this;
      if (me.$refs.formPaciente.validate()) {
        let accion = me.paciente.id == null ? "add" : "upd";
        me.loader = true;
        if(accion=="add"){
            axios.post(`/pacientes/save`,me.paciente)
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
                axios.put(`/pacientes/update`,me.paciente)
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
    deletePaciente(paciente) {
      let me = this;
      me.editedPaciente = me.arrayPacientes.indexOf(paciente);
      
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
        title: 'Eliminar Paciente',
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
            axios.post(`/pacientes/delete`, paciente)
            .then(function(response) {
                me.verificarAccionDato(response.data, response.status, "del");
                me.loader = false;
            })
          }
        });
    },
     verificarAccionDato(paciente, statusCode, accion) {
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
            //Agrego al array de pacientes el objecto que devuelve el backend
          this.fetchPacientes(); 
          Toast.fire({
            icon: 'success',
            title: 'Paciente registrado con exito'
           });
          me.loader = false;
          break;
        case "upd":
          //Actualizo al array de pacientes el objecto que devuelve el backend ya con los datos actualizados
          this.fetchPacientes(); 
          Toast.fire({
            icon: 'success',
            title: 'Paciente actualizado con exito'
           }); 
            
          me.loader = false;
          break;
        case "del":
          if (statusCode == 200) {
            try {
              //Se elimina del array de pacientes activos si todo esta bien en el backend
              me.arrayPacientes.splice(me.editedPaciente, 1);
              Toast.fire({
                icon: 'success',
                title: 'Paciente eliminado'
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
    me.fetchPacientes();
  }
};
</script>