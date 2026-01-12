from ..core.unified_entry_point_system import main

def upload_files(ftp, local_path, remote_path):
    for root, dirs, files in os.walk(local_path):
        for fname in files:
            full_fname = get_unified_utility().path.join(root, fname)
            relative_path = get_unified_utility().path.relpath(full_fname, local_path)
            remote_fname = get_unified_utility().path.join(remote_path, relative_path).replace('\\', '/')
            with open(full_fname, 'rb') as f:
                ftp.storbinary(f'STOR {remote_fname}', f)
            get_logger(__name__).info(f'Uploaded {full_fname} to {remote_fname}')


if __name__ == "__main__":
    main()
