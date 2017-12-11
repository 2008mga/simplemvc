// CommitChart.js
import { Line } from 'vue-chartjs'
import randomColor from 'randomcolor';

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
    randomColor,
    Init() {


      this.datasets.forEach((set) => {
        console.log(this.gradient);

        this.gradient = this.$refs.canvas.getContext('2d').createLinearGradient(0, 0, 0, 450);

        this.gradient.addColorStop(1, this.randomColor({
          luminosity: 'light',
          format: 'rgba',
          alpha: 0 // e.g. 'rgba(9, 1, 107, 0.5)',
        }));
        this.gradient.addColorStop(0.5, this.randomColor({
          luminosity: 'light',
          format: 'rgba',
          alpha: 0.5 // e.g. 'rgba(9, 1, 107, 0.5)',
        }));
        this.gradient.addColorStop(0, randomColor({
          luminosity: 'light',
          format: 'rgba',
          alpha: 1 // e.g. 'rgba(9, 1, 107, 0.5)',
        }));

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