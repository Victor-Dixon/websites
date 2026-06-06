# Bootstrap Hostinger SSH Persistence

generated=2026-06-06T15:12:29-05:00
root=/data/data/com.termux/files/home/projects/websites
domain=crosbyultimateevents.com
env_file=/data/data/com.termux/files/home/projects/websites/runtime/env/hostinger/sites/crosbyultimateevents.com.env
key_file=/data/data/com.termux/files/home/.ssh/hostinger_galaxy_ed25519

== PRECHECK ==
SSH_HOST=FOUND
SSH_PORT=65002
SSH_USER=FOUND

== INSTALL SSH TOOLS IF NEEDED ==

WARNING: apt does not have a stable CLI interface. Use with caution in scripts.

Hit:1 https://termux.net stable InRelease
Reading package lists...
Building dependency tree...
Reading state information...
15 packages can be upgraded. Run 'apt list --upgradable' to see them.

WARNING: apt does not have a stable CLI interface. Use with caution in scripts.

Reading package lists...
Building dependency tree...
Reading state information...
openssh is already the newest version (10.3p1-1).
Solving dependencies...
The following packages were automatically installed and are no longer required:
  abseil-cpp  fmt  libprotobuf
Use 'apt autoremove' to remove them.

Installing:
  sshpass

Summary:
  Upgrading: 0, Installing: 1, Removing: 0, Not Upgrading: 15
  Download size: 8440 B
  Space needed: 69.6 kB

Get:1 https://termux.net stable/main aarch64 sshpass aarch64 1.10-1 [8440 B]
Fetched 8440 B in 0s (24.2 kB/s)
Selecting previously unselected package sshpass.
(Reading database ... (Reading database ... 5%(Reading database ... 10%(Reading database ... 15%(Reading database ... 20%(Reading database ... 25%(Reading database ... 30%(Reading database ... 35%(Reading database ... 40%(Reading database ... 45%(Reading database ... 50%(Reading database ... 55%(Reading database ... 60%(Reading database ... 65%(Reading database ... 70%(Reading database ... 75%(Reading database ... 80%(Reading database ... 85%(Reading database ... 90%(Reading database ... 95%(Reading database ... 100%(Reading database ... 36281 files and directories currently installed.)
Preparing to unpack .../sshpass_1.10-1_aarch64.deb ...
Unpacking sshpass (1.10-1) ...
Setting up sshpass (1.10-1) ...

== GENERATE KEY IF MISSING ==
Generating public/private ed25519 key pair.
Your identification has been saved in /data/data/com.termux/files/home/.ssh/hostinger_galaxy_ed25519
Your public key has been saved in /data/data/com.termux/files/home/.ssh/hostinger_galaxy_ed25519.pub
The key fingerprint is:
SHA256:91R9YDwpxR9L6IFhbKNWMAv8LStdRXAluwvv7IBUNy4 galaxy-hostinger-crosbyultimateevents.com
The key's randomart image is:
+--[ED25519 256]--+
|      .. oo+=B*o |
|       .. +*o=B= |
|        ..=.+=+o=|
|         =.oooo.o|
|        S.=E.o   |
|       ..+.o+ .  |
|        .. ..o   |
|            +    |
|            .+   |
+----[SHA256]-----+
KEY_CREATED=/data/data/com.termux/files/home/.ssh/hostinger_galaxy_ed25519

== SEED KNOWN HOST ==
KNOWN_HOST=SEEDED

== PASSWORD PROMPT LOCAL ONLY ==

== INSTALL PUBLIC KEY ON HOSTINGER ==
** WARNING: connection is not using a post-quantum key exchange algorithm.
** This session may be vulnerable to "store now, decrypt later" attacks.
** The server may need to be upgraded. See https://openssh.com/pq.html
AUTHORIZED_KEY=PASS

== VERIFY KEY LOGIN ==
** WARNING: connection is not using a post-quantum key exchange algorithm.
** This session may be vulnerable to "store now, decrypt later" attacks.
** The server may need to be upgraded. See https://openssh.com/pq.html
/home/u996867598
u996867598
CROSBY_ROOT=PASS

== UPDATE CROSBY ENV KEY PATH ==
ENV_KEY_PATH_UPDATED=PASS

== CLOSEOUT ==
STATUS=HOSTINGER_SSH_PERSISTENCE_READY
REPORT=/data/data/com.termux/files/home/projects/websites/data/reports/websites/bootstrap_hostinger_ssh_persistence_crosbyultimateevents.com_20260606_151229.md
