#!/usr/bin/env python3
"""
Convert All Mission Reports Devlogs to Digital Dreamscape Episodes
"""

from sample_devlog_converter import SampleDevlogConverter

def main():
    print("🚀 Starting Mission Reports Conversion")
    print("=" * 50)

    converter = SampleDevlogConverter("D:/websites/digitaldreamscape.site")

    # Convert all mission reports
    mission_episodes = converter.convert_mission_reports_full()

    print("\n📊 Conversion Summary:")
    print(f"   Episodes Created: {len(mission_episodes)}")
    print(f"   Episode Range: EP-{mission_episodes[0]['episode_id']} to EP-{mission_episodes[-1]['episode_id']}")
    print("   Category: Mission Reports")
    print("   Questline: swarm mission coordination")
    print("\n✅ Mission reports successfully converted to Digital Dreamscape episodes!")
    print("\n🔗 Access episodes at: episodes/ep_[number]_mission-reports_episode.html")
    print("🔗 Questline page: questlines/mission-reports.html")
if __name__ == "__main__":
    main()