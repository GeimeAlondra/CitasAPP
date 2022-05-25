<template>
<v-data-table
  :headers="hTBCitas"
  :items="arrayCitas"
  :items-per-page="5"
  class="elevation-1"
  ></v-data-table>
</template>

<script>
  export default {
    props:['user'],
   data: () => ({
      arrayCitas: [],
      hTBCitas: [
        { text: "Fecha", value: "fecha", align: "center" },
        { text: "Horario", value: "horario", align: "center" },
        { text: "Dentista", value: "dentista", align: "center"  },
      ],
      loader: false,
      search: "",
      estado:"R",
  }),

    methods:{

    fetchCitas() {
      let me = this;
      me.loader = true;
        axios.get(`/reservas/user?user=`+me.id)
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
    
    },
    mounted(){
      let me = this;
      me.fetchCitas(me.id);
      console.log(this.user);
    }
  }
</script>