#Get filename
# echo "type filename:"
# read fname
fname="001"

#remove
# rm -rf output
# rm -rf rep_set_align
# rm -rf blast_output
# rm -rf pynast_output
# rm -rf uclust_output
# rm -rf parallel_blast
# rm -rf rdp_output
# rm -rf biom_otu_pick
# rm "${fname}_match_primer.fastq"
# rm "${fname}_match_bprimer.fastq"
# rm "${fname}_match_bprimer_rc.fastq"
# rm "${fname}_concat.fastq"
# rm "${fname}_match_primer.fa"
# rm "${fname}_match_primer_derep.fa"
# rm "${fname}_match_primer_sorted.fa"
# rm "${fname}_otus1.fa"
# rm "${fname}_map.uc"
# rm "${fname}_match_fbprimer.fastq"
# rm "${fname}_match_fbprimer2.fastq"
find ./ -maxdepth 1 -type d -regex '.*/[a-z0-9]+' | xargs rm -rf
find ./ -maxdepth 1 -type f -regex '.*/[a-z0-9]+\.zip' | xargs rm -rf
echo "delete succeeded"