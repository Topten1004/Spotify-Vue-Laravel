<script>
import { Line } from 'vue-chartjs'
export default {
  extends: Line,
  props: ['chartdata', 'options','reset'],
  mounted () {
    this.renderChart(this.chartdata, this.options)
  },
  watch: {
    reset(val) {
      if( val ) {
        this.renderChart(this.chartdata, this.options)
        this.$emit('resetOver')
      }
    }
  }
}
</script>
