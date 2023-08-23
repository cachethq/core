<template>
  <div class="flex flex-col gap-y-4 py-4 -mt-4">
    <Header />

    <StatusBar />

    <ComponentGroup v-for="group in componentGroups" :key="group.id" :name="group.name" :components="group.components" :collapsable="group.collapsable" />

    <IncidentTimeline>
      <IncidentGroup v-for="(incidents, timestamp) in reportedIncidents" :key="`incident-group-${timestamp}`" :incidents="incidents" :timestamp="timestamp" />
    </IncidentTimeline>

    <Footer />
  </div>
</template>

<script>
import StatusPage from '@/Layouts/Dashboard.vue'
import StatusBar from './Components/StatusBar.vue'
import ComponentGroup from './Components/ComponentGroup.vue'
import IncidentTimeline from '@/Pages/StatusPage/Components/IncidentTimeline.vue'
import IncidentGroup from '@/Pages/StatusPage/Components/IncidentGroup.vue'
import Header from '@/Pages/StatusPage/Components/Header.vue'
import Footer from '@/Pages/StatusPage/Components/Footer.vue'

export default {
  components: {
    Footer,
    Header,
    IncidentGroup,
    IncidentTimeline,
    ComponentGroup,
    StatusBar,
  },
  layout: [StatusPage],
  computed: {
    componentGroups() {
      return [
        {
          id: 1,
          name: 'Services',
          collapsable: true,
          components: [
            {
              id: 1,
              name: 'Laravel Artisan Cheatsheet',
              status: 1,
              human_readable_status: 'Operational',
            },
            {
              id: 2,
              name: 'Checkmango',
              status: 1,
              human_readable_status: 'Operational',
            },
          ],
        },
        {
          id: 2,
          name: 'Other Components',
          collapsable: false,
          components: [
            {
              id: 3,
              name: 'API',
              status: 2,
              human_readable_status: 'Performance Issues',
            },
            {
              id: 4,
              name: 'Queues',
              status: 3,
              human_readable_status: 'Partial Outage',
            },
            {
              id: 5,
              name: 'Notifications',
              status: 4,
              human_readable_status: 'Major Outage',
            },
          ],
        },
      ]
    },
    // This would be a prop...
    reportedIncidents() {
      return {
        '2023-08-15': [{
          id: 1,
          name: 'API Outage',
          description: 'We are currently experiencing an outage with our API.',
          created_at: '2023-08-15 20:38:00',
          updates: [{
            id: 1,
            name: 'Identified the issue',
            description: 'We identified the issue. Our DNS provider is currently experiencing an outage and we are working on a solution.',
            status: 1,
            human_readable_status: 'Identified',
            created_at: '2023-08-15 20:40:00',
          }, {
            id: 2,
            name: 'Resolved the issue',
            description: 'We\'ve switched DNS provider.',
            status: 4,
            human_readable_status: 'Resolved',
            created_at: '2023-08-15 20:50:00',
          }],
        }],
        '2023-08-14': [], // There will be an option for removing non-incident dates from the timeline.
        '2023-08-13': [{
          id: 1,
          name: 'Queues Down',
          description: 'Queues were broken',
          created_at: '2023-08-13 20:38:00',
          updates: [],
        }],
      }
    },
  },
}
</script>
