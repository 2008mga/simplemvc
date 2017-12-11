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
  data() {
    return {
      gradient: null
    }
  },
  methods: {
    getRandomColor() {
      let letters = '0123456789ABCDEF';
      let color = '#';
      for (let i = 0; i < 6; i++) {
        color += letters[Math.floor(Math.random() * 16)];
      }
      return color;
    },
    Init() {


      this.datasets.forEach((set) => {
        console.log(this.gradient);

        this.gradient = this.$refs.canvas.getContext('2d').createLinearGradient(0, 0, 0, 450);

        this.gradient.addColorStop(1, this.getRandomColor());

        set = Object.assign(set, {
          backgroundColor: this.gradient
        });

        return set;
      });

      this.renderChart({
        labels: this.labels,
        datasets: this.datasets,
        gradient: null
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