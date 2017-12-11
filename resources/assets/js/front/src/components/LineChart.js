// CommitChart.js
import { Line } from 'vue-chartjs'

export default {
  extends: Line,
  props: ['labels', 'datasets'],
  watch: {
    'labels'() {
      this.$data._chart.destroy();
      this.$emit('data::change');
    },
    'datasets'() {
      this.$data._chart.destroy();
      this.$emit('data::change');
    }

  },
  methods: {
    Init() {
      this.renderChart({
        labels: this.labels,
        datasets: this.datasets
      });


    }
  },
  mounted () {
    this.Init();
    this.$on('data::change', () => {
      this.Init();
    });
  }
}